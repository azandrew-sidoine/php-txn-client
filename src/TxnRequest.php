<?php


namespace Drewlabs\TxnClient;

use InvalidArgumentException;
use Stringable;

class TxnRequest implements TxnRequestInterface
{
    /**
     * 
     * @var string|Stringable
     */
    private $uri;

    /**
     * 
     * @var string
     */
    private $method;

    /**
     * 
     * @var TxnRequestBodyInterface
     */
    private $body;

    /**
     * 
     * @var string
     */
    private $version;

    /**
     * 
     * @var TxnRequestCredentialsInterface
     */
    private $credentials;

    public function __construct(
        $uri,
        $method,
        TxnRequestBodyInterface $body,
        string $version = '1.1'
    ) {
        $this->uri = $uri;
        $this->method = $method;
        $this->body = $body;
        $this->version = $version;
    }

    public function getUri()
    {
        return $this->uri;
    }

    public function withUri($uri)
    {
        $this->uri = $uri;
        return $this;
    }

    public function getMethod()
    {
        return $this->method ?? 'GET';
    }

    public function withMethod(string $method)
    {
        if (false === preg_match('/GET|POST|PUT|DELETE|HEAD|OPTION/i', $method)) {
                throw new InvalidArgumentException("Unsupprted request method " . (string)$method . ". Supported values are GET, POST, PUT, DELETE, HEAD, OPTION");
        }
        $this->method = $method;
        return $this;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function withBody(TxnRequestBodyInterface $body)
    {
        $this->body = $body;
        return $this;
    }

    public function getCredentials()
    {
        return $this->credentials;
    }

    public function withCredentials($credentialOrKey, string $secret = null)
    {
        if ($credentialOrKey instanceof TxnRequestCredentialsInterface) {
            $this->credentials = $credentialOrKey;
        } else {
            $this->credentials = new Credentials($credentialOrKey, $secret);
        }
        return $this;
    }

    public function getProtocolVersion()
    {
        return $this->version ?? '1.1';
    }

    public function withProtocolVersion($version)
    {
        if (!is_numeric($version)) {
            throw new InvalidArgumentException('HTTP protocol versin must be a valid protocol version');
        }
        $this->version = $version;
        return $this->version;
    }
}
