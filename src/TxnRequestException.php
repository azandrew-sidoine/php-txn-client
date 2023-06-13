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

class TxnRequestException extends \Exception
{
    /**
     * @var int
     */
    private $statusCode;

    /**
     * @var TxnRequestInterface
     */
    private $request;

    /**
     * @var array
     */
    private $headers;

    /**
     * Creates class instance.
     */
    public function __construct(TxnRequestInterface $request, int $statusCode, array $responseHeaders = [], string $message = 'Client Error')
    {
        parent::__construct($message ?? 'Client Error', $statusCode);
        $this->request = $request;
        $this->statusCode = $statusCode;
        $this->headers = $responseHeaders ?? [];

    }

    /**
     * returns the status code value.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * return response headers.
     */
    public function getHeaders(): array
    {
        return $this->headers ?? [];
    }

    /**
     * return request object.
     */
    public function getRequest(): TxnRequestInterface
    {
        return $this->request;
    }
}
