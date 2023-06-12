<?php

declare(strict_types=1);

/*
 * This file is part of the drewlabs namespace.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\TxnClient;

class TxnRequest implements TxnRequestInterface
{
    /**
     * @var string|\Stringable
     */
    private $uri;

    /**
     * @var string
     */
    private $method;

    /**
     * @var TxnRequestBodyInterface
     */
    private $body;

    /**
     * @var string
     */
    private $version;

    /**
     * Creates an instance of {@see \Drewlabs\TxnClient\TxnRequest} class.
     *
     * @param mixed $uri
     * @param mixed $method
     *
     * @return void
     */
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

    public function clone()
    {
        return clone $this;
    }

    public function __clone()
    {
        if ($this->body) {
            $this->body = clone $this->body->clone();
        }

        if ($this->uri && \is_object($this->uri)) {
            $this->uri = clone $this->uri;
        }
    }

    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Copy the request with new request uri.
     *
     * **Note** Implementation does not modify the original request uri,
     * instead it creates a copy of the object and modify uri of the copy.
     *
     * @return static
     */
    public function withUri($uri)
    {
        $this->uri = $uri;

        return $this;
    }

    public function getMethod()
    {
        return $this->method ?? 'GET';
    }

    /**
     * Copy the request with new request method.
     *
     * **Note** Implementation does not modify the original request method,
     * instead it creates a copy of the object and modify method of the copy.
     *
     * @throws \InvalidArgumentException
     *
     * @return static
     */
    public function withMethod(string $method)
    {
        if (false === preg_match('/GET|POST|PUT|DELETE|HEAD|OPTION|TRACE/i', $method)) {
            throw new \InvalidArgumentException('Unsupprted request method ' . (string) $method . '. Supported values are GET, POST, PUT, DELETE, HEAD, OPTION');
        }
        $object = clone $this;
        $object->method = $method;

        return $object;
    }

    public function getBody()
    {
        return $this->body;
    }

    /**
     * Copy the request with new request body.
     *
     * **Note** Implementation does not modify the original request body,
     * instead it creates a copy of the object and modify body of the copy.
     *
     * @return static
     */
    public function withBody(TxnRequestBodyInterface $body)
    {
        $object = clone $this;
        $object->body = $body;

        return $object;
    }

    public function getProtocolVersion()
    {
        return $this->version ?? '1.1';
    }

    /**
     * Copy the request with a new protocol version.
     *
     * **Note** Implementation does not modify the original request http protocol
     * version, instead it creates a copy of the object and modify the protocol version
     * of the copy.
     *
     * @param string $version
     *
     * @throws \InvalidArgumentException
     *
     * @return static
     */
    public function withProtocolVersion($version)
    {
        if (!is_numeric($version)) {
            throw new \InvalidArgumentException('HTTP protocol versin must be a valid protocol version');
        }
        $object = clone $this;
        $object->version = $version;

        return $object;
    }
}
