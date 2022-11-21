<?php

namespace Drewlabs\TxnClient;

interface TxnRequestBodyInterface extends Arrayable
{
    /**
     * Returns the string representation of the object
     * 
     * @return string 
     */
    public function __toString();

    /**
     * Get the response config of the current request object
     * 
     * @return HTTPResponseConfigInterface 
     */
    public function getResponseConfig();

    /**
     * Get the txn property
     * 
     * @return TxnInterface 
     */
    public function getTxn();
}