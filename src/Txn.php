<?php

namespace Drewlabs\TxnClient;

use InvalidArgumentException;

class Txn implements TxnInterface
{
    /**
     * Txn payment url property
     * 
     * @var string
     */
    private $paymentUri;

    /**
     * Creates an instance of {@see \Drewlabs\TxnClient\Txn} class
     */
    public function __construct()
    {
        // Provide constructor implementation 
    }

    /**
     * Creates {@see \Drewlabs\TxnClient\Txn} instance from json structure (dictionnary/object) 
     * 
     * @param object|array $attributes 
     * @return self 
     */
    public static function create($attributes)
    {
        if (is_object($attributes)) {
            $attributes = get_object_vars($attributes);
        }
        if (!is_array($attributes)) {
            throw new InvalidArgumentException("Expected PHP array or object type, got " . (is_object($attributes) && !is_null($attributes) ? get_class($attributes) : gettype($attributes)));
        }
        // TODO : Provides deserialization to Txn type implementation
    }

    public function id()
    {
    }

    public function getPaymentUri()
    {
        return $this->paymentUri;
    }
}
