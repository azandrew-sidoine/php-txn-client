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
     * Txn amount property
     * 
     * @var float
     */
    private $amount;

    /**
     * Txn currency property
     * 
     * @var string
     */
    private $currency;

    /**
     * Txn processors property
     * 
     * @var string[]
     */
    private $processors;

    /**
     * Txn label property
     * 
     * @var string|null
     */
    private $label;

    /**
     * Txn debtor property
     * 
     * @var string|null
     */
    private $debtor;

    /**
     * Creates an instance of {@see \Drewlabs\TxnClient\Txn} class
     * 
     * @param string $reference 
     * @param float|int $amount
     * @param string[] $processors
     * @param string $currency 
     * @param string $label 
     * @param string $debtor 
     * @param string|int $id 
     * @param string $payment_url 
     */
    public function __construct(
        string $reference,
        float $amount,
        array $processors,
        $currency = 'XOF',
        string $label = null,
        string $debtor = null,
        $id = null,
        string $payment_url = null
    ) {
        $this->payment_url = $payment_url;
        $this->id = $id;
        $this->reference = $reference;
        $this->currency = $currency;
        $this->amount = $amount;
        $this->processors = $this->setProcessors($processors);
        $this->label = $label;
        $this->debtor = $debtor;
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
        throw new UnexpectedValueException('Expect parameter of Txn::create() to be an array, ');
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
     * Set the amount property
     * 
     * @param float $value 
     * @return static 
     */
    public function setAmount(float $amount)
    {
        return $this->merge('amount', $amount);
    }


    /**
     * Set the processors property
     * 
     * @param array<string> $value 
     * @return static 
     */
    public function setProcessors(array $processors)
    {
        return $this->merge('processors', array_map(function ($processor) {
            return (string)$processor;
        }, $processors ?? []));
    }

    /**
     * Set the currency property
     * 
     * @param string $value 
     * @return static 
     */
    public function setCurrency(string $value)
    {
        return $this->merge('currency', $value);
    }

    /**
     * Set the label property
     * 
     * @param string $value 
     * @return static 
     */
    public function setLabel(string $value)
    {
        return $this->merge('label', $value);
    }

    /**
     * Set the response config of the current request object
     * 
     * @param Arrayable $value 
     * 
     * @return static 
     */
    public function setDebtor(string $value)
    {
        return $this->merge('debtor', $value);
    }

    /**
     * Merge the instance property to modify in the existing properties
     * 
     * @param string $attribute 
     * @param mixed $value 
     * @return object 
     */
    protected function merge(string $attribute, $value)
    {
        /**
         * @var object|\stdClass
         */
        $object = clone ($this);
        $object->__set($attribute, $value);
        return $object;
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

    /**
     * Get the amount property
     * 
     * @return float 
     */
    public function getAmount()
    {
        return $this->amount;
    }


    /**
     * Get the processors property
     * 
     * @return string[]
     */
    public function getProcessors()
    {
        return $this->processors;
    }

    /**
     * Get the currency property
     * 
     * @return string 
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Get the label property
     * 
     * @return string|null 
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Get the debtor property
     * 
     * @return string|null 
     */
    public function getDebtor()
    {
        return $this->debtor;
    }
}
