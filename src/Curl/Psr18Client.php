<?php

namespace Drewlabs\TxnClient\Curl;

use Drewlabs\TxnClient\Http\NetworkException;
use Drewlabs\TxnClient\Http\RequestException;
use Drewlabs\TxnClient\Http\Response;
use Drewlabs\TxnClient\Http\Uri;
use Exception;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use ReflectionException;

/**
 * @method static Psr18Client new(string $base_url, array $options = [])
 * 
 * @package Drewlabs\TxnClient\Curl
 */
class Psr18Client implements ClientInterface
{
    /**
     * 
     * @var Client
     */
    private $client;

    /**
     * 
     * @var string
     */
    private $base_url;

    private function __construct()
    {
    }

    /**
     * Creates an instance of {@see Psr18Client}
     * 
     * @param mixed $args 
     * @return Psr18Client 
     * @throws ReflectionException 
     */
    public static function new(...$args)
    {
        /**
         * @var Psr18Client
         */
        $instance = (new \ReflectionClass(__CLASS__))->newInstanceWithoutConstructor();
        $instance->client = new Client(null, ...array_slice($args, 1));
        $instance->base_url = $args[0] ?? null;
        return $instance;
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        $uri = $request->getUri() ?? Uri::new();
        if (null !== $this->base_url) {
            $uri = $uri->withHost(rtrim($this->base_url, '/'));
        }
        $request = $request->withUri($uri);
        $this->client->setOptions($this->prepareCurlRequest($request));
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


    private function prepareCurlRequest(RequestInterface $request)
    {
        return $this->appendCurlBodyIfNotEmpty(
            $request,
            $this->appendCurlHeaders(
                $request,
                $this->curlDefaults($request)
            )
        );
    }


    private function curlDefaults(RequestInterface $request)
    {
        $defaults = [
            '__HEADERS__'              => $request->getHeaders(),
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


    private function appendCurlHeaders(RequestInterface $request, callable $callback)
    {
        $options = $callback($request);
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

    private function appendCurlBody(RequestInterface $request, callable $callback)
    {
        $options = $callback($request);
        $size = $request->hasHeader('Content-Length') ? (int) $request->getHeaderLine('Content-Length') : null;

        // Send the body as a string if the size is less than 1MB OR if the
        // [curl][body_as_string] request value is set.
        if (($size !== null && $size < 1000000)) {
            $options[\CURLOPT_POSTFIELDS] = (string) $request->getBody();
            // Don't duplicate the Content-Length header
            // $this->removeHeader('Content-Length', $conf);
            // $this->removeHeader('Transfer-Encoding', $conf);
        } else {
            $options[\CURLOPT_UPLOAD] =  true;
            if ($size !== null) {
                $options[\CURLOPT_INFILESIZE] =  $size;
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

    private function appendCurlBodyIfNotEmpty(RequestInterface $request, callable $callback)
    {
        $options = $callback($request);
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

    function createPipe(...$pipeline)
    {
        return function (RequestInterface $request, \Closure $next) use ($pipeline) {
            $nextFunc = function (RequestInterface $request, \Closure $interceptor) {
                return $interceptor($request, function ($request) {
                    return $request;
                });
            };
            $stack = [function ($request) use (&$next) {
                return $next($request);
            }];
            if (count($pipeline) === 0) {
                $pipeline = [function (RequestInterface $request, \Closure $callback) {
                    return $callback($request);
                }];
            }
            foreach (\array_reverse($pipeline) as $func) {
                $previous = array_pop($stack);
                if (!is_callable($previous)) {
                    throw new Exception('Interceptor function must be a callable instance');
                }
                array_push($stack, function ($request) use (&$func, &$previous) {
                    return $func($request, $previous);
                });
            }
            return $nextFunc($request, array_pop($stack));
        };
    }
}
