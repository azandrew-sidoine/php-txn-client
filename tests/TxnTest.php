<?php

namespace Drewlabs\TxnClient\Tests;

use Drewlabs\TxnClient\Tests\Utils;
use Drewlabs\TxnClient\Txn;
use Drewlabs\TxnClient\TxnInterface;
use PHPUnit\Framework\TestCase;

class TxnTest extends TestCase
{
    public function test_txn_constructor_create_txn_interface_without_exception()
    {
        $txn = new Txn('TR-6248790-BVHF', 43000, ['Ecobank']);
        $this->assertInstanceOf(TxnInterface::class, $txn);
    }

    public function test_txn_constructor_set_required_properties()
    {
        $ref = 'TR-6248790-BVHF';
        $amount = 23000.00;
        $processors = ['Ecobank'];
        $txn = new Txn($ref, $amount, $processors);

        $this->assertEquals($ref, $txn->getReference());
        $this->assertEquals($amount, $txn->getAmount());
        $this->assertEquals($processors, $txn->getProcessors());
    }


    public function test_txn_immutability()
    {
        $ref = 'TR-6248790-BVHF';
        $amount = 23000.00;
        $processors = ['Ecobank'];
        $txn = new Txn($ref, $amount, $processors);

        $txn2 = $txn->setReference('TR-7248-78242HI-OF898')
            ->setAmount(44000)
            ->setProcessors(['CorisMoney']);

        $this->assertFalse($txn->getReference() === $txn2->getReference());
        $this->assertFalse($txn->getProcessors() === $txn2->getProcessors());
        $this->assertFalse($txn->getAmount() === $txn2->getAmount());
    }

    public function test_txn_fluent_api_methods()
    {
        $txn = new Txn('TR8204-8924JKOFE', 52000, ['TestProcessor']);

        $txnid = Utils::guidv4();
        $txn2 = $txn->setId($txnid)
            ->setReference('TR980-97348-98742O')
            ->setAmount(70500)
            ->setPaymentUrl('http://127.0.0.1:7000/payment.php')
            ->setCurrency('USD')
            ->setLabel('TEST INVOICE PAYMENT')
            ->setDebtor('AZLABS\'s SARL U.')
            ->setProcessors(['flooz']);
        $this->assertEquals($txnid, $txn2->getId());
        $this->assertEquals('TR980-97348-98742O', $txn2->getReference());
        $this->assertEquals(70500, $txn2->getAmount());
        $this->assertEquals('http://127.0.0.1:7000/payment.php', $txn2->getPaymentUrl());
        $this->assertEquals('USD', $txn2->getCurrency());
        $this->assertEquals('TEST INVOICE PAYMENT', $txn2->getLabel());
        $this->assertEquals('AZLABS\'s SARL U.', $txn2->getDebtor());
        $this->assertEquals(['flooz'], $txn2->getProcessors());


        $this->assertEquals('TR8204-8924JKOFE', $txn->getReference());
        $this->assertEquals(52000, $txn->getAmount());
        $this->assertEquals(['TestProcessor'], $txn->getProcessors());
        $this->assertTrue(null === $txn->getPaymentUrl());
        $this->assertTrue('XOF' === $txn->getCurrency());
        $this->assertTrue(null === $txn->getLabel());
        $this->assertTrue(null === $txn->getDebtor());
    }
}
