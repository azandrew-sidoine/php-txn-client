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

        $this->assertSame($ref, $txn->getReference());
        $this->assertSame($amount, $txn->getAmount());
        $this->assertSame($processors, $txn->getProcessors());
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
        $this->assertSame($txnid, $txn2->getId());
        $this->assertSame('TR980-97348-98742O', $txn2->getReference());
        $this->assertSame(round(70500, 2), round($txn2->getAmount(), 2));
        $this->assertSame('http://127.0.0.1:7000/payment.php', $txn2->getPaymentUrl());
        $this->assertSame('USD', $txn2->getCurrency());
        $this->assertSame('TEST INVOICE PAYMENT', $txn2->getLabel());
        $this->assertSame('AZLABS\'s SARL U.', $txn2->getDebtor());
        $this->assertSame(['flooz'], $txn2->getProcessors());

        $this->assertSame('TR8204-8924JKOFE', $txn->getReference());
        $this->assertSame(round(52000, 2), round($txn->getAmount(), 2));
        $this->assertSame(['TestProcessor'], $txn->getProcessors());
        $this->assertTrue(null === $txn->getPaymentUrl());
        $this->assertTrue('XOF' === $txn->getCurrency());
        $this->assertTrue(null === $txn->getLabel());
        $this->assertTrue(null === $txn->getDebtor());
    }
}
