<?php

namespace Drewlabs\Curl;

use Drewlabs\Psr7\NetworkException;
use Drewlabs\Psr7\RequestException;
use Drewlabs\Psr7\Response;
use Drewlabs\Psr7\Uri;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use ReflectionException;

/**
 * @method static Psr18Client new(string $base_url, array $options = [])
 * 
 * @package Drewlabs\Curl
 */
class Psr18Client implements ClientInterface
{
    use AppendsClientOptions;
    /**
     * 
     * @var Client
     */
    private $client;

    /**
     * 
     * @var ClientOptions
     */
    private $options;

    private function __construct()
    {
    }

    /**
     * Creates an instance of {@see Psr18Client}
     * 
     * @param string $base_url
     * @param ClientOptions $options 
     * @return Psr18Client 
     * @throws ReflectionException 
     */
    public static function new(string $base_url = null, ClientOptions $options = null)
    {
        /**
         * @var Psr18Client
         */
        $instance = (new \ReflectionClass(__CLASS__))->newInstanceWithoutConstructor();
        $instance->client = new Client(null, []);
        $instance->options = $options ?? new ClientOptions();
        if ($base_url) {
            $instance->options->baseURL($base_url);
        }
        return $instance;
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $uri = $request->getUri() ?? Uri::new();
        if (null !== $this->base_url) {
            $uri = $uri->withHost(rtrim($this->base_url, '/'));
        }
        $request = $request->withUri($uri);
        $options = $this->prepareCurlRequest($request);
        unset($options['__HEADERS__']);
        $this->client->setOptions($options);
        $this->client->execute();
        if (($errorno = $this->client->getError()) !== 0) {
            // Throw Error if client return error code different from 0
            [$exceptionMessage, $errorCode] = [$this->client->getErrorMessage(), CurlError::toHTTPStatusCode($errorno)];
            if (
                in_array($errorno, [
                    CurlError::CURLE_COULDNT_CONNECT,
                    CurlError::CURLE_COULDNT_RESOLVE_HOST,
                    CurlError::CURLE_COULDNT_RESOLVE_PROXY,
                ])
            ) {
                throw new NetworkException($request, $exceptionMessage, $errorCode);
            }
            throw new RequestException($request, $exceptionMessage, $errorCode);
        }
        $statusCode = $this->client->getStatusCode();
        return new Response(
            $statusCode < 100 && 511 > $statusCode ? 500 : CurlError::toHTTPStatusCode($errorno),
            $this->client->getResponseHeaders(),
            $this->client->getResponse(),
            $this->client->getProtocolVersion(),
            $this->client->hasErrorr() ? $this->client->getErrorMessage() : null
        );
    }

    /**
     * Prepare the curl request
     * 
     * @param RequestInterface $request 
     * @return array 
     */
    public function prepareCurlRequest(RequestInterface $request)
    {
        return $this->appendCurlHeaders(
            $request,
            $this->appendClientOptions(
                $request,
                $this->options,
                $this->appendCurlBodyIfNotEmpty(
                    $request,
                    $this->curlDefaults($request)
                )
            )
        );
    }

    /**
     * 
     * @param RequestInterface $request 
     * @return (string[][]|string|false|int)[] 
     */
    private function curlDefaults(RequestInterface $request)
    {
        $defaults = [
            '__HEADERS__'           => $request->getHeaders(),
            \CURLOPT_CUSTOMREQUEST  => $request->getMethod(),
            \CURLOPT_URL            => (string) $request->getUri()->withFragment(''),
            \CURLOPT_RETURNTRANSFER => false,
            \CURLOPT_HEADER         => false,
            \CURLOPT_CONNECTTIMEOUT => 150,
        ];
        if (\defined('CURLOPT_PROTOCOLS')) {
            $defaults[\CURLOPT_PROTOCOLS] = \CURLPROTO_HTTP | \CURLPROTO_HTTPS;
        }
        $version = $request->getProtocolVersion();
        if ($version == 1.1) {
            $defaults[\CURLOPT_HTTP_VERSION] = \CURL_HTTP_VERSION_1_1;
        } elseif ($version == 2.0) {
            $defaults[\CURLOPT_HTTP_VERSION] = \CURL_HTTP_VERSION_2_0;
        } else {
            $defaults[\CURLOPT_HTTP_VERSION] = \CURL_HTTP_VERSION_1_0;
        }
        return $defaults;
    }


    /**
     * 
     * @param RequestInterface $request 
     * @param array $options 
     * @return mixed 
     */
    private function appendCurlHeaders(RequestInterface $request, array $options)
    {
        // $options = $callback($request);
        foreach ($options['__HEADERS__'] as $name => $values) {
            foreach ($values as $value) {
                $value = (string) $value;
                if ($value === '') {
                    // cURL requires a special format for empty headers.
                    // See https://github.com/guzzle/guzzle/issues/1882 for more details.
                    $options[\CURLOPT_HTTPHEADER][] = "$name;";
                } else {
                    $options[\CURLOPT_HTTPHEADER][] = "$name: $value";
                }
            }
        }
        // Remove the Accept header if one was not set
        if (!$request->hasHeader('Accept')) {
            $options[\CURLOPT_HTTPHEADER][] = 'Accept:';
        }
        return $options;
    }

    /**
     * 
     * @param RequestInterface $request 
     * @param array $options
     * @return mixed 
     */
    private function appendCurlBody(RequestInterface $request, array $options)
    {
        // $options = $callback($request);
        $size = $request->hasHeader('Content-Length') ? (int) $request->getHeaderLine('Content-Length') : null;
        if (($size !== null && $size < 1000000)) {
            $options[\CURLOPT_POSTFIELDS] = (string) $request->getBody();
            // Don't duplicate the Content-Length header
            $options = $this->removeHeaders($options, 'Content-Length', 'Transfer-Encoding');
        } else {
            $options[\CURLOPT_UPLOAD] =  true;
            if ($size !== null) {
                $options[\CURLOPT_INFILESIZE] =  $size;
                $options = $this->removeHeaders($options, 'Content-Length');
            }
            $body = $request->getBody();
            if ($body->isSeekable()) {
                $body->rewind();
            }
            $options[\CURLOPT_READFUNCTION] = static function ($curl, $fd, $length) use ($body) {
                return $body->read($length);
            };
        }

        // If the Expect header is not present, prevent curl from adding it
        if (!$request->hasHeader('Expect')) {
            $options[\CURLOPT_HTTPHEADER][] = 'Expect:';
        }

        // cURL sometimes adds a content-type by default. Prevent this.
        if (!$request->hasHeader('Content-Type')) {
            $options[\CURLOPT_HTTPHEADER][] = 'Content-Type:';
        }
        return $options;
    }

    /**
     * 
     * @param RequestInterface $request 
     * @param array $options
     * @return mixed 
     */
    private function appendCurlBodyIfNotEmpty(RequestInterface $request, array $options)
    {
        // $options = $callback($request);
        [$body, $size] = [($body = $request->getBody()), $body->getSize()];
        if ($size === null || $size > 0) {
            return $this->appendCurlBody($request, $options);
        }
        $method = $request->getMethod();
        if ($method === 'PUT' || $method === 'POST') {
            // See https://tools.ietf.org/html/rfc7230#section-3.3.2
            if (!$request->hasHeader('Content-Length')) {
                $options[\CURLOPT_HTTPHEADER][] = 'Content-Length: 0';
            }
        } elseif ($method === 'HEAD') {
            $options[\CURLOPT_NOBODY] = true;
            unset(
                $options[\CURLOPT_WRITEFUNCTION],
                $options[\CURLOPT_READFUNCTION],
                $options[\CURLOPT_FILE],
                $options[\CURLOPT_INFILE]
            );
        }
        return $options;
    }

    /**
     * Remove a header from the options array.
     *
     * @param array  $options Array of options to modify
     * @param string[] $name    Case-insensitive header to remove
     */
    private function removeHeaders(array $options, ...$names)
    {
        foreach ($names as $name) {
            foreach (\array_keys($options['__HEADERS__']) as $key) {
                if (!\strcasecmp($key, $name)) {
                    unset($options['__HEADERS__'][$key]);
                    return;
                }
            }
        }
        return $options;
    }
}
