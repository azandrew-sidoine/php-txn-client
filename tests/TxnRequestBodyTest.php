<?php

use Drewlabs\TxnClient\HTTPResponseConfig;
use Drewlabs\TxnClient\Tests\Utils;
use Drewlabs\TxnClient\Txn;
use Drewlabs\TxnClient\TxnRequestBody;
use Drewlabs\TxnClient\TxnRequestBodyInterface;
use PHPUnit\Framework\TestCase;

class TxnRequestBodyTest extends TestCase
{

    public function test_txn_request_body_create_new_instance_without_error()
    {
        $requestBody = new TxnRequestBody(new Txn(Utils::guidv4(), 45000, ['TestProcessor']));
        $this->assertInstanceOf(TxnRequestBodyInterface::class, $requestBody);
    }

    public function test_txn_request_body_fluent_api_methods()
    {
        $txn = new Txn(Utils::guidv4(), 50000, ['TestProcessor']);
        $requestBody = new TxnRequestBody($txn);
        $response = HTTPResponseConfig::create([
            'txn_reference_key' => 't_ref',
            'txn_time_key' => 't_time',
            'txn_amount_key' => 't_montant',
            'txn_id_key' => 't_id',
            'txn_processor_key' => 't_processor',
            'request_options' => ['azlabsapi', 'Zwdhdw2nxl6HigJ688IGtrw5cqQQKbiF', 2]
        ]);
        $requestBody = $requestBody->setResponseConfig($response);
        $this->assertEquals($response, $requestBody->getResponseConfig());

        $txn2 = new Txn(Utils::guidv4(), 62000, ['flooz']);
        $requestBody = $requestBody->setTxn($txn2);
        $this->assertNotEquals($response, $requestBody->getTxn());
        $this->assertEquals($txn2, $requestBody->getTxn());
    }


    public function test_txn_request_body_immutability()
    {
        $txn = new Txn(Utils::guidv4(), 50000, ['TestProcessor']);

        $txnRequestBody = new TxnRequestBody($txn);

        $txn2 = new Txn(Utils::guidv4(), 62000, ['flooz']);

        $txnRequestBody2 = $txnRequestBody->setTxn($txn2)
            ->setResponseConfig(HTTPResponseConfig::create());

        $this->assertNotEquals($txnRequestBody->getTxn(), $txnRequestBody2->getTxn());
        $this->assertNotEquals($txnRequestBody->getResponseConfig(), $txnRequestBody2->getResponseConfig());
        $this->assertTrue(null === $txnRequestBody->getResponseConfig());
    }
}
