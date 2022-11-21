<?php

namespace Drewlabs\TxnClient;

interface HTTPResponseRequestOptionInterface extends Arrayable
{

    /**
     * Return the response request option key
     * 
     * @return string 
     */
    public function getKey();

    /**
     * Return the response request option value
     * 
     * @return string 
     */
    public function getValue();

    /**
     * Return the response request option type
     * 
     * @return string 
     */
    public function getType();

}