<?php

namespace Drewlabs\TxnClient;

use Stringable;

interface TxnRequestInterface
{
    /**
     * returns the HTTP request uri provided by the current instance
     * 
     * @return string|Stringable 
     */
    public function getUri();

    /**
     * returns an instance with the provided URI.
     * 
     * @param string|Stringable $uri
     * 
     * @return static 
     */
    public function withUri($uri);

    /**
     * returns the HTTP verb used when sending request
     * 
     * @return string 
     */
    public function getMethod();

    /**
     * returns an instance with the provided HTTP method.
     * 
     * @return static 
     */
    public function withMethod(string $method);

    /**
     * returns the request body of the current request
     * 
     * @return string|TxnRequestBodyInterface
     */
    public function getBody();

    /**
     * returns an instance with the specified message body.
     * 
     * @param TxnRequestBodyInterface $body
     * 
     * @return static 
     */
    public function withBody(TxnRequestBodyInterface $body);

    /**
     * returns the provided HTTP protocol version.
     * 
     * @return static 
     */
    public function getProtocolVersion();

    /**
     * returns an instance with the specified HTTP protocol version.
     *
     * The version string MUST contain only the HTTP version number (e.g.,
     * "1.1", "1.0").
     *
     * @param string $version HTTP protocol version
     * @return static
     */
    public function withProtocolVersion($version);
}
