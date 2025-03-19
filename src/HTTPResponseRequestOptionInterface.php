<?php

namespace Drewlabs\TxnClient;

interface HTTPResponseRequestOptionInterface extends Arrayable
{

    /**
     * returns the response request option key
     * 
     * @return string 
     */
    public function getKey();

    /**
     * returns the response request option value
     * 
     * @return string 
     */
    public function getValue();

    /**
     * returns the response request option type
     * 
     * @return string 
     */
    public function getType();

}