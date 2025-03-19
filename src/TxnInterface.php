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
    public function getPaymentUrl();

    /**
     * returns the unique identifier of the transaction (txn)
     * on the gateway platform
     * 
     * @return string 
     */
    public function getId();

    /**
     * returns the transaction reference
     * 
     * @return string 
     */
    public function getReference();

    /**
     * Get the amount property
     * 
     * @return float 
     */
    public function getAmount();


    /**
     * Get the processors property
     * 
     * @return string[]
     */
    public function getProcessors();

    /**
     * Get the currency property
     * 
     * @return string 
     */
    public function getCurrency();
    
    /**
     * Get the label property
     * 
     * @return string|null 
     */
    public function getLabel();

    /**
     * Get the debtor property
     * 
     * @return string|null 
     */
    public function getDebtor();
}