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

class PaymentResult
{
    /**
     * @var string
     */
    private $txnRef;

    /**
     * @var string
     */
    private $txnTime;

    /**
     * @var float|int
     */
    private $txnAmount;

    /**
     * @var string|int
     */
    private $txnId;

    /**
     * @var string|int
     */
    private $processorTxnId;

    /**
     * @var string
     */
    private $txnPayeerId;

    /**
     * Creates class instance
     * 
     * @param string $ref 
     * @param string $time 
     * @param int|float $amount 
     * @param string $id 
     * @param string $mouvement 
     * @param string $payeer 
     */
    public function __construct(
        string $ref = null,
        string $time = null,
        $amount = 0,
        string $id = null,
        string $mouvement = null,
        string $payeer = null,
    ) {
        $this->txnRef = $ref;
        $this->txnTime = $time;
        $this->txnAmount = $amount;
        $this->txnId = $id;
        $this->processorTxnId = $mouvement;
        $this->txnPayeerId = $payeer;
    }

    /**
     * set the transaction reference property value.
     *
     * @return static
     */
    public function withTxnReference(string $value)
    {
        $self = clone $this;
        $self->txnRef = $value;
        return $self;
    }

    /**
     * set the transaction time property value.
     *
     * @return static
     */
    public function withTxnTime(string $value)
    {
        $self = clone $this;
        $self->txnTime = $value;
        return $self;
    }

    /**
     * set the transaction amount / value property value.
     *
     * @param int|float $value
     * 
     * @return static
     */
    public function withTxnAmount($value)
    {
        $self = clone $this;
        $self->txnAmount = $value;
        return $self;
    }

    /**
     * Get the transaction id property value.
     *
     * @return static
     */
    public function withTxnId(string $value)
    {
        $self = clone $this;
        $self->txnId = $value;
        return $self;
    }

    /**
     * set the transaction id on the processor platform property value.
     *
     * @return static
     */
    public function withProcessorTxnId(string $value)
    {
        $self = clone $this;
        $self->processorTxnId = $value;
        return $self;
    }

    /**
     * set the transaction payeer id property.
     *
     * @return static
     */
    public function withTxnPayeerId(string $value)
    {
        $self = clone $this;
        $self->txnPayeerId = $value;
        return $self;
    }

    /**
     * Get the transaction reference property value.
     *
     * @return string
     */
    public function getTxnReference()
    {
        return $this->txnRef;
    }

    /**
     * Get the transaction time property value.
     *
     * @return string
     */
    public function getTxnTime()
    {
        return $this->txnTime;
    }

    /**
     * Get the transaction amount / value property value.
     *
     * @return string
     */
    public function getTxnAmount()
    {
        return round(floatval($this->txnAmount ?? 0), 2);
    }

    /**
     * Get the transaction id property value.
     *
     * @return string
     */
    public function getTxnId()
    {
        return $this->txnId;
    }

    /**
     * Get the transaction id on the processor platform property value.
     *
     * @return string
     */
    public function getProcessorTxnId()
    {
        return $this->processorTxnId;
    }

    /**
     * Get the transaction payeer id property.
     *
     * @return string
     */
    public function getTxnPayeerId()
    {
        return $this->txnPayeerId;
    }
}
