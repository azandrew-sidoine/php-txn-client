<?php

namespace Drewlabs\TxnClient;

interface TxnRequestCredentialsInterface
{
    /**
     * Authorization secret property getter method
     * 
     * @return string 
     */
    public function getKey();

    /**
     * Authorization secret property getter method
     * 
     * @return string 
     */
    public function getSecret();
}