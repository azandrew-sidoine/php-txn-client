<?php

namespace Drewlabs\Curl;

use Drewlabs\Psr7\CreatesJSONStream;
use Drewlabs\Psr7\CreatesMultipartStream;
use Drewlabs\Psr7\CreatesURLEncodedStream;
use Drewlabs\Psr7Stream\LazyStream;
use Drewlabs\Psr7Stream\Stream;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use RuntimeException;

trait AppendsClientOptions
{

    /**
     * Override psr7 request with request options
     * 
     * @param RequestInterface $request 
     * @param ClientOptions $clientOptions 
     * @return RequestInterface 
     * @throws InvalidArgumentException 
     */
    private function overrideRequest(RequestInterface $request, ClientOptions $clientOptions): RequestInterface
    {

        $requestOptions = $clientOptions->getRequest();
        if (null === $requestOptions) {
            return $request;
        }
        // Get the request uri in temparary variable and check later if it changes 
        // to update the request query
        $uri = $request->getUri();
        $body = $request->getBody();
        $contentTypeHeader = empty($result = $request->getHeader('Content-Type')) ? '' : implode(',', $result);

        if (!empty($headers = $requestOptions->getHeaders())) {
            if (array_keys($headers) === range(0, count($headers) - 1)) {
                throw new InvalidArgumentException('The headers array must have header name as keys.');
            }
        }
        // Find the content type header from the request option headers
        foreach ($headers as $key => $value) {
            if (strtolower($key) === 'content-type') {
                $contentTypeHeader = (string)$value;
            }
        }
        $optionsBody = $requestOptions->getBody();
        if (!empty($contentTypeHeader) && preg_match('/^multipart\/form-data/', $contentTypeHeader) && !empty($optionsBody)) {
            // Handle request of multipart http request
            $createsStream = new CreatesMultipartStream($optionsBody);
            $body = new LazyStream($createsStream);
            $headers['Content-Type'] = 'multipart/form-data; boundary=' . $createsStream->getBoundary();
        } else if (!empty($contentTypeHeader) && (false !== preg_match('/^(?:application|text)\/(?:[a-z]+(?:[\.-][0-9a-z]+){0,}[\+\.]|x-)?json(?:-[a-z]+)?/i', $contentTypeHeader))) {
            // Handle JSON request
            $body = new LazyStream(new CreatesJSONStream($optionsBody));
            $headers['Content-Type'] = 'application/json';
        } else {
            // Handle URL encoded request
            $body = new LazyStream(new CreatesURLEncodedStream($optionsBody));
            $headers['Content-Type'] = 'application/x-www-form-urlencoded';
        }

        if (!empty($query = $requestOptions->getQuery())) {
            if (\is_array($query)) {
                $query = \http_build_query($query, '', '&', \PHP_QUERY_RFC3986);
            }
            if (!\is_string($query)) {
                throw new InvalidArgumentException('query must be a string or array');
            }
            $uri = $uri->withQuery($query);
        }

        if (!empty($encoding = $requestOptions->getEncoding()) && $encoding !== true) {
            // Ensure that we don't have the header in different case and set the new value.
            $headers['Accept-Encoding'] = $encoding;
        }

        if ($uri !== $request->getUri()) {
            $request = $request->withUri($uri);
        }

        if (!empty($headers)) {
            foreach ($headers as $name => $value) {
                $request = $request->withHeader($name, $value);
            }
        }

        if ($body !== $request->getBody()) {
            $request = $request->withBody($body);
        }

        return $request;
    }

    /**
     * 
     * @param RequestInterface $request 
     * @param ClientOptions $clientOptions 
     * @param array $output 
     * @return array 
     * @throws InvalidArgumentException 
     * @throws RuntimeException 
     */
    private function appendClientOptions(RequestInterface $request, ClientOptions $clientOptions, array $output)
    {
        if (null !== ($verify = $clientOptions->verify())) {
            if ($verify === false) {
                unset($output[\CURLOPT_CAINFO]);
                $output[\CURLOPT_SSL_VERIFYHOST] = 0;
                $output[\CURLOPT_SSL_VERIFYPEER] = false;
            } else {
                $output[\CURLOPT_SSL_VERIFYHOST] = 2;
                $output[\CURLOPT_SSL_VERIFYPEER] = true;
                if (\is_string($verify)) {
                    if (!\file_exists($verify)) {
                        throw new \InvalidArgumentException("SSL CA bundle not found: {$verify}");
                    }
                    // If it's a directory or a link to a directory use CURLOPT_CAPATH.
                    // If not, it's probably a file, or a link to a file, so use CURLOPT_CAINFO.
                    if (
                        \is_dir($verify) ||
                        (\is_link($verify) === true &&
                            ($verifyLink = \readlink($verify)) !== false &&
                            \is_dir($verifyLink)
                        )
                    ) {
                        $output[\CURLOPT_CAPATH] = $verify;
                    } else {
                        $output[\CURLOPT_CAINFO] = $verify;
                    }
                }
            }
        }

        $requestOptions = $clientOptions->getRequest();

        if ($requestOptions->getEncoding()) {
            if ($accept = $request->getHeaderLine('Accept-Encoding')) {
                $output[\CURLOPT_ENCODING] = $accept;
            } else {
                $output[\CURLOPT_ENCODING] = '';
                $output[\CURLOPT_HTTPHEADER][] = 'Accept-Encoding:';
            }
        }

        if (empty($clientOptions->sink())) {
            $clientOptions->sink(Stream::new('', 'w+'));
        }
        $sink = $clientOptions->sink();
        if (!\is_string($sink)) {
            $sink = Stream::new($sink);
        } elseif (!\is_dir(\dirname($sink))) {
            // Ensure that the directory exists before failing in curl.
            throw new \RuntimeException(\sprintf('Directory %s does not exist for sink value of %s', \dirname($sink), $sink));
        } else {
            // TODO : Provide a lazy stream implementation
            $sink = new LazyStream(function () use ($sink) {
                return Stream::new($sink, 'w+');
            });
        }
        $output[\CURLOPT_WRITEFUNCTION] = static function ($ch, $write) use ($sink) {
            return $sink->write($write);
        };

        $timeoutRequiresNoSignal = false;
        if ($timeout = $clientOptions->timeout()) {
            $timeoutRequiresNoSignal |= $timeout < 1;
            $output[\CURLOPT_TIMEOUT_MS] = $timeout * 1000;
        }

        // CURL default value is CURL_IPRESOLVE_WHATEVER
        if ($ip = $clientOptions->forceResolveIp()) {
            if ('v4' === $ip) {
                $output[\CURLOPT_IPRESOLVE] = \CURL_IPRESOLVE_V4;
            } elseif ('v6' === $ip) {
                $output[\CURLOPT_IPRESOLVE] = \CURL_IPRESOLVE_V6;
            }
        }

        if ($connectTimeout = $clientOptions->connectTimeout()) {
            $timeoutRequiresNoSignal |= $connectTimeout < 1;
            $output[\CURLOPT_CONNECTTIMEOUT_MS] = $connectTimeout * 1000;
        }

        if ($timeoutRequiresNoSignal && \strtoupper(\substr(\PHP_OS, 0, 3)) !== 'WIN') {
            $output[\CURLOPT_NOSIGNAL] = true;
        }

        if ($proxy = $clientOptions->proxy()) {
            $output[CURLOPT_PROXY] = $proxy[0];
            if (isset($proxy[1])) {
                $output[CURLOPT_PROXYPORT] = $proxy[1];
            }
            if (isset($proxy[2]) && isset($proxy[3])) {
                $output[CURLOPT_PROXYUSERPWD] = $proxy[2] . ':' . $proxy[3];
            }
        }

        if ($cert = $clientOptions->cert()) {
            $certFile = $cert[0];
            if (count($cert) === 2) {
                $output[\CURLOPT_SSLCERTPASSWD] = $cert[1];
            }
            if (!\file_exists($certFile)) {
                throw new \InvalidArgumentException("SSL certificate not found: {$certFile}");
            }
            # OpenSSL (versions 0.9.3 and later) also support "P12" for PKCS#12-encoded files.
            # see https://curl.se/libcurl/c/CURLOPT_SSLCERTTYPE.html
            $ext = pathinfo($certFile, \PATHINFO_EXTENSION);
            if (preg_match('#^(der|p12)$#i', $ext)) {
                $output[\CURLOPT_SSLCERTTYPE] = strtoupper($ext);
            }
            $output[\CURLOPT_SSLCERT] = $certFile;
        }

        if ($sslKeyOptions = $clientOptions->sslKey()) {
            if (\count($sslKeyOptions) === 2) {
                [$sslKey, $output[\CURLOPT_SSLKEYPASSWD]] = $sslKeyOptions;
            } else {
                [$sslKey] = $sslKeyOptions;
            }
            if (!\file_exists($sslKey)) {
                throw new \InvalidArgumentException("SSL private key not found: {$sslKey}");
            }
            $output[\CURLOPT_SSLKEY] = $sslKey;
        }

        if ($progress = $clientOptions->progress()) {
            if (!\is_callable($progress)) {
                throw new \InvalidArgumentException('progress client option must be callable');
            }
            $output[\CURLOPT_NOPROGRESS] = false;
            $output[\CURLOPT_PROGRESSFUNCTION] = static function ($resource, int $downloadSize, int $downloaded, int $uploadSize, int $uploaded) use ($progress) {
                $progress($downloadSize, $downloaded, $uploadSize, $uploaded);
            };
        }
        return $output;
    }

    private function applyAuthOptions(RequestOptions $requestOptions, array $output)
    {

        if (!empty($auth = $requestOptions->getAuth()) && \is_array($auth)) {
            $type = isset($auth[2]) ? \strtolower($auth[2]) : 'basic';
            switch ($type) {
                case 'basic':
                    $output['__HEADERS__']['Authorization'] = 'Basic ' . \base64_encode("$auth[0]:$auth[1]");
                    break;
                case 'digest':
                    // TODO: In future release, find an implementation that build a digest auth algorithm
                    $output['curl'][\CURLOPT_HTTPAUTH] = \CURLAUTH_DIGEST;
                    $output['curl'][\CURLOPT_USERPWD] = "$auth[0]:$auth[1]";
                    break;
                case 'ntlm':
                    $output['curl'][\CURLOPT_HTTPAUTH] = \CURLAUTH_NTLM;
                    $output['curl'][\CURLOPT_USERPWD] = "$auth[0]:$auth[1]";
                    break;
            }
        }
        return $output;
    }
}
