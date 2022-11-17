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
}