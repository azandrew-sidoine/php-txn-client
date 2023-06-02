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

use Drewlabs\TxnClient\Tests\Utils;
use Drewlabs\TxnClient\Txn;
use Drewlabs\TxnClient\TxnRequest;
use Drewlabs\TxnClient\TxnRequestBody;
use Drewlabs\TxnClient\TxnRequestInterface;
use PHPUnit\Framework\TestCase;

class TxnRequestTest extends TestCase
{
    public function test_txn_request_constructor_creates_instance_without_error()
    {
        $txnRequest = new TxnRequest('http://127.0.0.1:3000/api/transactions', 'POST', $this->createRequestBody());
        $this->assertInstanceOf(TxnRequestInterface::class, $txnRequest);
    }

    public function test_txn_request_fluent_api_methods()
    {
        $requestBody = $this->createRequestBody();
        $txnRequest = new TxnRequest('http://127.0.0.1:3000/api/transactions', 'POST', $requestBody);

        $this->assertSame('http://127.0.0.1:3000/api/transactions', $txnRequest->getUri());
        $this->assertSame('POST', $txnRequest->getMethod());
        $this->assertSame($requestBody, $txnRequest->getBody());

        $updatedBody = $this->createRequestBody();
        $txnRequest = $txnRequest->withMethod('GET')
            ->withProtocolVersion('2.0')
            ->withBody($updatedBody)
            ->withUri('http://127.0.0.1:8000/api/transactions');
        $this->assertNotSame('http://127.0.0.1:3000/api/transactions', $txnRequest->getUri());
        $this->assertNotSame('POST', $txnRequest->getMethod());
        $this->assertNotSame($requestBody, $txnRequest->getBody());

        $this->assertSame('http://127.0.0.1:8000/api/transactions', $txnRequest->getUri());
        $this->assertSame('GET', $txnRequest->getMethod());
        $this->assertSame($updatedBody, $txnRequest->getBody());
        $this->assertSame('2.0', $txnRequest->getProtocolVersion());
    }

    public function test_txn_request_immutability()
    {
        $requestBody = $this->createRequestBody();
        $txnRequest = new TxnRequest('http://127.0.0.1:3000/api/transactions', 'POST', $requestBody);

        $updatedBody = $this->createRequestBody();
        $txnRequest2 = $txnRequest->withMethod('GET')
            ->withProtocolVersion('2.0')
            ->withBody($updatedBody)
            ->withUri('http://127.0.0.1:8000/api/transactions');

        $this->assertNotSame($txnRequest2->getUri(), $txnRequest->getUri());
        $this->assertNotSame($txnRequest2->getMethod(), $txnRequest->getMethod());
        $this->assertNotSame($txnRequest2->getBody(), $txnRequest->getBody());
        $this->assertNotSame($txnRequest2->getProtocolVersion(), $txnRequest->getProtocolVersion());
    }

    private function createRequestBody()
    {
        return new TxnRequestBody(new Txn(Utils::guidv4(), 45000, ['TestProcessor']));
    }
}
