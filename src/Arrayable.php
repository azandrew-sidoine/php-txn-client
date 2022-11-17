<?php

namespace Drewlabs\TxnClient;

interface Arrayable
{
    /**
     * Returns a PHP array representation of the current instance
     * 
     * @return array 
     */
    public function toArray();
}