<?php

namespace Drewlabs\TxnClient;

trait HasContructorFactory
{
    /**
     * Creates an instance of the current class
     * 
     * @param mixed $args 
     * @return self 
     */
    public static function new(...$args)
    {
        return new static(...$args);
    }
}