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

interface TxnRequestInterface
{
    /**
     * Returns the HTTP request uri provided by the current instance.
     *
     * @return string|\Stringable
     */
    public function getUri();

    /**
     * Returns an instance with the provided URI.
     *
     * @param string|\Stringable $uri
     *
     * @return static
     */
    public function withUri($uri);

    /**
     * Returns the HTTP verb used when sending request.
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
     * Returns the request body of the current request.
     *
     * @return string|TxnRequestBodyInterface
     */
    public function getBody();

    /**
     * Return an instance with the specified message body.
     *
     * @return static
     */
    public function withBody(TxnRequestBodyInterface $body);

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
     *
     * @return static
     */
    public function withProtocolVersion($version);

    /**
     * Clone the current object.
     *
     * @return static
     */
    public function clone();
}
