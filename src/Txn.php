<?php

namespace Drewlabs\TxnClient;

use InvalidArgumentException;
use Stringable;
use UnexpectedValueException;

class Txn implements TxnInterface
{
    use ArrayInstanciable;

    /**
     * Txn payment url property
     * 
     * @var string
     */
    private $payment_url;

    /**
     * Txn id property
     * 
     * @var string
     */
    private $id;

    /**
     * Txn reference property
     * 
     * @var string
     */
    private $reference;

    /**
     * Creates an instance of {@see \Drewlabs\TxnClient\Txn} class
     * 
     * @param string|int $id 
     * @param string $payment_url 
     * @param string $reference 
     */
    public function __construct($id = null, string $payment_url = null, string $reference = null)
    {
        $this->payment_url = $payment_url;
        $this->id = $id;
        $this->reference = $reference;
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
        };
        if (is_array($attributes)) {
            return self::createFromArray($attributes);
        }
        return new static();
    }

    /**
     * Set the txn id property
     * 
     * @param string|int $value 
     * @return static 
     */
    public function setId($value)
    {
        $this->id = $value;
        return $this;
    }

    /**
     * Returns the txn payment url 
     * 
     * @return string|Stringable 
     */
    public function setPaymentUrl(string $url)
    {
        if (false === ($components = @parse_url($url))) {
            throw new UnexpectedValueException('$url parameter must be a valid resource url');
        }
        if (!in_array(strtolower($components['scheme'] ?? ''), ['http', 'https'])) {
            throw new UnexpectedValueException('Payment URL must be a valid HTTP resource URL');
        }
        $this->payment_url = $url;
        return $this;
    }

    /**
     * Returns the transaction reference
     * 
     * @param string $ref 
     * @return $this 
     */
    public function setReference(string $ref)
    {
        $this->reference = $ref;
        return $this;
    }

    /**
     * Returns the txn id
     * 
     * @return string 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the txn payment url 
     * 
     * @return string|Stringable 
     */
    public function getPaymentUrl()
    {
        return $this->payment_url;
    }

    /**
     * Returns the transaction reference
     * 
     * @return string 
     */
    public function getReference()
    {
        return $this->reference;
    }
}
