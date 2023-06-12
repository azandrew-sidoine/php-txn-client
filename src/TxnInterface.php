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

interface TxnInterface
{
    /**
     * Return the txn payment uri interface|string that to which
     * a user must be redirect in order to process the transaction.
     *
     * @return string|\Stringable
     */
    public function getPaymentUrl();

    /**
     * Returns the unique identifier of the transaction (txn)
     * on the gateway platform.
     *
     * @return string
     */
    public function getId();

    /**
     * Returns the transaction reference.
     *
     * @return string
     */
    public function getReference();

    /**
     * Get the amount property.
     *
     * @return float
     */
    public function getAmount();

    /**
     * Get the processors property.
     *
     * @return string[]
     */
    public function getProcessors();

    /**
     * Get the currency property.
     *
     * @return string
     */
    public function getCurrency();

    /**
     * Get the label property.
     *
     * @return string|null
     */
    public function getLabel();

    /**
     * Get the debtor property.
     *
     * @return string|null
     */
    public function getDebtor();

    /**
     * Clone the current object
     * 
     * @return static 
     */
    public function clone();
}
