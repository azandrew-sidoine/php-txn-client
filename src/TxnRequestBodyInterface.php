<?php

namespace Drewlabs\TxnClient;

interface TxnRequestBodyInterface extends Arrayable
{
    /**
     * returns the string representation of the object
     * 
     * @return string 
     */
    public function __toString();

    /**
     * returns the response config of the current request object
     * 
     * @return HTTPResponseConfigInterface 
     */
    public function getResponseConfig();

    /**
     * returns the txn property
     * 
     * @return TxnInterface 
     */
    public function getTxn();
}