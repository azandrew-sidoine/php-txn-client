<?php

namespace Drewlabs\Curl;

use Drewlabs\Psr7Stream\Stream;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use RuntimeException;

trait AppendsClientOptions
{

    // if (isset($options['headers'])) {
    //     if (array_keys($options['headers']) === range(0, count($options['headers']) - 1)) {
    //         throw new InvalidArgumentException('The headers array must have header name as keys.');
    //     }
    //     $modify['set_headers'] = $options['headers'];
    //     unset($options['headers']);
    // }

    // if (isset($options['form_params'])) {
    //     if (isset($options['multipart'])) {
    //         throw new InvalidArgumentException('You cannot use '
    //             . 'form_params and multipart at the same time. Use the '
    //             . 'form_params option if you want to send application/'
    //             . 'x-www-form-urlencoded requests, and the multipart '
    //             . 'option to send multipart/form-data requests.');
    //     }
    //     $options['body'] = \http_build_query($options['form_params'], '', '&');
    //     unset($options['form_params']);
    //     // Ensure that we don't have the header in different case and set the new value.
    //     $options['_conditional'] = Psr7\Utils::caselessRemove(['Content-Type'], $options['_conditional']);
    //     $options['_conditional']['Content-Type'] = 'application/x-www-form-urlencoded';
    // }

    // if (isset($options['multipart'])) {
    //     $options['body'] = new Psr7\MultipartStream($options['multipart']);
    //     unset($options['multipart']);
    // }

    // if (isset($options['json'])) {
    //     $options['body'] = Utils::jsonEncode($options['json']);
    //     unset($options['json']);
    //     // Ensure that we don't have the header in different case and set the new value.
    //     $options['_conditional'] = Psr7\Utils::caselessRemove(['Content-Type'], $options['_conditional']);
    //     $options['_conditional']['Content-Type'] = 'application/json';
    // }

    // if (!empty($options['decode_content'])
    //     && $options['decode_content'] !== true
    // ) {
    //     // Ensure that we don't have the header in different case and set the new value.
    //     $options['_conditional'] = Psr7\Utils::caselessRemove(['Accept-Encoding'], $options['_conditional']);
    //     $modify['set_headers']['Accept-Encoding'] = $options['decode_content'];
    // }

    // if (isset($options['body'])) {
    //     if (\is_array($options['body'])) {
    //         throw $this->invalidBody();
    //     }
    //     $modify['body'] = Psr7\Utils::streamFor($options['body']);
    //     unset($options['body']);
    // }

    // if (!empty($options['auth']) && \is_array($options['auth'])) {
    //     $value = $options['auth'];
    //     $type = isset($value[2]) ? \strtolower($value[2]) : 'basic';
    //     switch ($type) {
    //         case 'basic':
    //             // Ensure that we don't have the header in different case and set the new value.
    //             $modify['set_headers'] = Psr7\Utils::caselessRemove(['Authorization'], $modify['set_headers']);
    //             $modify['set_headers']['Authorization'] = 'Basic '
    //                 . \base64_encode("$value[0]:$value[1]");
    //             break;
    //         case 'digest':
    //             // @todo: Do not rely on curl
    //             $options['curl'][\CURLOPT_HTTPAUTH] = \CURLAUTH_DIGEST;
    //             $options['curl'][\CURLOPT_USERPWD] = "$value[0]:$value[1]";
    //             break;
    //         case 'ntlm':
    //             $options['curl'][\CURLOPT_HTTPAUTH] = \CURLAUTH_NTLM;
    //             $options['curl'][\CURLOPT_USERPWD] = "$value[0]:$value[1]";
    //             break;
    //     }
    // }

    // if (isset($options['query'])) {
    //     $value = $options['query'];
    //     if (\is_array($value)) {
    //         $value = \http_build_query($value, '', '&', \PHP_QUERY_RFC3986);
    //     }
    //     if (!\is_string($value)) {
    //         throw new InvalidArgumentException('query must be a string or array');
    //     }
    //     $modify['query'] = $value;
    //     unset($options['query']);
    // }

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

        if ($clientOptions->decodeContent()) {
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
            $sink = Stream::new($sink, 'w+');
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
}
