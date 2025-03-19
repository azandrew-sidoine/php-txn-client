<?php

namespace Drewlabs\TxnClient\Tests;

use Drewlabs\TxnClient\Tests\Utils;
use Drewlabs\TxnClient\Txn;
use Drewlabs\TxnClient\TxnRequest;
use Drewlabs\TxnClient\TxnRequestBody;
use Drewlabs\TxnClient\TxnRequestInterface;
use PHPUnit\Framework\TestCase;

class TxnRequestTest extends TestCase
{
    private function createRequestBody()
    {
        return new TxnRequestBody(new Txn(Utils::guidv4(), 45000, ['TestProcessor']));
    }

    public function test_txn_request_constructor_creates_instance_without_error()
    {
        $txnRequest = new TxnRequest('http://127.0.0.1:3000/api/transactions', 'POST', $this->createRequestBody());
        $this->assertInstanceOf(TxnRequestInterface::class, $txnRequest);
    }

    public function test_txn_request_fluent_api_methods()
    {
        $requestBody = $this->createRequestBody();
        $txnRequest = new TxnRequest('http://127.0.0.1:3000/api/transactions', 'POST', $requestBody);

        $this->assertEquals('http://127.0.0.1:3000/api/transactions', $txnRequest->getUri());
        $this->assertEquals('POST', $txnRequest->getMethod());
        $this->assertEquals($requestBody, $txnRequest->getBody());

        $updatedBody = $this->createRequestBody();
        $txnRequest = $txnRequest->withMethod('GET')
            ->withProtocolVersion('2.0')
            ->withBody($updatedBody)
            ->withUri('http://127.0.0.1:8000/api/transactions');
        $this->assertNotEquals('http://127.0.0.1:3000/api/transactions', $txnRequest->getUri());
        $this->assertNotEquals('POST', $txnRequest->getMethod());
        $this->assertNotEquals($requestBody, $txnRequest->getBody());

        $this->assertEquals('http://127.0.0.1:8000/api/transactions', $txnRequest->getUri());
        $this->assertEquals('GET', $txnRequest->getMethod());
        $this->assertEquals($updatedBody, $txnRequest->getBody());
        $this->assertEquals('2.0', $txnRequest->getProtocolVersion());
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

        $this->assertNotEquals($txnRequest2->getUri(), $txnRequest->getUri());
        $this->assertNotEquals($txnRequest2->getMethod(), $txnRequest->getMethod());
        $this->assertNotEquals($txnRequest2->getBody(), $txnRequest->getBody());
        $this->assertNotEquals($txnRequest2->getProtocolVersion(), $txnRequest->getProtocolVersion());
    }
}
