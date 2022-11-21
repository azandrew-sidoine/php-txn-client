<?php

namespace Drewlabs\TxnClient;

use Stringable;

interface TxnRequestInterface
{
    /**
     * Returns the HTTP request uri provided by the current instance
     * 
     * @return string|Stringable 
     */
    public function getUri();

    /**
     * Returns an instance with the provided URI.
     * 
     * @param string|Stringable $uri
     * 
     * @return static 
     */
    public function withUri($uri);

    /**
     * Returns the HTTP verb used when sending request
     * 
     * @return string 
     */
    public function getMethod();

    /**
     * Return an instance with the provided HTTP method.
     * 
     * @return static 
     */
    public function withMethod(string $method);

    /**
     * Returns the request body of the current request
     * 
     * @return string|TxnRequestBodyInterface
     */
    public function getBody();

    /**
     * Return an instance with the specified message body.
     * 
     * @param TxnRequestBodyInterface $body
     * 
     * @return static 
     */
    public function withBody(TxnRequestBodyInterface $body);

    /**
     * Returns the credentials provided by the instance
     * 
     * @return TxnRequestCredentialsInterface 
     */
    public function getCredentials();

    /**
     * Returns an instance with the provided credentials.
     * 
     * @param TxnRequestCredentialsInterface|string $credentialOrKey 
     * @param string|null $secret 
     * @return mixed 
     */
    public function withCredentials($credentialOrKey, string $secret = null);

    /**
     * Return the provided HTTP protocol version.
     * 
     * @return static 
     */
    public function getProtocolVersion();

    /**
     * Return an instance with the specified HTTP protocol version.
     *
     * The version string MUST contain only the HTTP version number (e.g.,
     * "1.1", "1.0").
     *
     * @param string $version HTTP protocol version
     * @return static
     */
    public function withProtocolVersion($version);
}
