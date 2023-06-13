<?php

use Drewlabs\TxnClient\HTTPResponseConfig;
use Drewlabs\TxnClient\PaymentResultFactory;
use PHPUnit\Framework\TestCase;

class PaymentResultFactoryTest extends TestCase
{

    public function test_payment_result_factory_create_defaults()
    {
        $factory = new PaymentResultFactory();

        $txnId = sprintf("TXN-%s%s", time(), rand(1000, 100000));
        $processorTxnId = sprintf("P-TXN-%s%s", time(), rand(1000, 100000));
        $at = date('Y-m-d H:i:s');

        // Act
        $result = $factory->createPaymentResult([
            't_ref' => 'T-REF-830535',
            't_time' => $at,
            't_montant' => 3400,
            't_id' => $txnId,
            't_processor_id' => $processorTxnId,
            't_payeer' => '22890775623'
        ]);
    
        // Assert
        $this->assertEquals($processorTxnId, $result->getProcessorTxnId());
        $this->assertEquals(round(floatval(3400), 2), $result->getTxnAmount());
        $this->assertEquals($txnId, $result->getTxnId());
        $this->assertEquals('22890775623', $result->getTxnPayeerId());
        $this->assertEquals($at, $result->getTxnTime());
        $this->assertEquals('T-REF-830535', $result->getTxnReference());
    }

    public function test_payment_result_factory_create_custom()
    {

        $response = HTTPResponseConfig::create([
            'txn_reference_key' => 't_reference',
            'txn_time_key' => 't_time',
            'txn_amount_key' => 't_amount',
            'txn_id_key' => 't_id',
            'txn_processor_key' => 't_processor'
        ]);
        $factory = new PaymentResultFactory($response);

        $txnId = sprintf("TXN-%s%s", time(), rand(1000, 100000));
        $processorTxnId = sprintf("P-TXN-%s%s", time(), rand(1000, 100000));
        $at = date('Y-m-d H:i:s');

        // Act
        $result = $factory->createPaymentResult([
            't_reference' => 'T-REF-830535',
            't_time' => $at,
            't_amount' => 3400,
            't_id' => $txnId,
            't_processor' => $processorTxnId,
            't_payeer' => '22890775623'
        ]);
    
        // Assert
        $this->assertEquals($processorTxnId, $result->getProcessorTxnId());
        $this->assertEquals(round(floatval(3400), 2), $result->getTxnAmount());
        $this->assertEquals($txnId, $result->getTxnId());
        $this->assertEquals('22890775623', $result->getTxnPayeerId());
        $this->assertEquals($at, $result->getTxnTime());
        $this->assertEquals('T-REF-830535', $result->getTxnReference());

    }
}