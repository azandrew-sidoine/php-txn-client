<?php

namespace Drewlabs\TxnClient;

use Stringable;

interface TxnInterface
{
    /**
     * Return the txn payment uri interface|string that to which
     * a user must be redirect in order to process the transaction
     * 
     * @return string|Stringable
     */
    public function getPaymentUri();

    /**
     * Returns the unique identifier of the transaction (txn)
     * on the gateway platform
     * 
     * @return string 
     */
    public function id();
}