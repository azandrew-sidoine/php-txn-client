<?php

namespace Drewlabs\TxnClient;

use InvalidArgumentException;

/**
 * @method static HTTPResponseRequestOption new(string $key, string $value, $type = 1)
 * 
 * @package Drewlabs\TxnClient
 */
class HTTPResponseRequestOption implements HTTPResponseRequestOptionInterface
{
    use HasContructorFactory;

    /** @var int */
    private $type;

    /** @var string */
    private $key;

    /** @var string */
    private $value;

    /**
     * Creates a new {@see \Drewlabs\TxnClient\HTTPResponseRequestOption} instance
     * 
     * @param string $key 
     * @param string $value 
     * @param int $type 
     * @return void 
     */
    public function __construct(string $key, string $value, $type = 1)
    {
        $this->key = $key;
        $this->value = $value;
        $this->type = $type ?? 1;
    }


    /**
     * Creates a new instance of {@see \Drewlabs\TxnClient\HTTPResponseRequestOption} classs
     * 
     * @param self|array $options 
     * @return static 
     * @throws InvalidArgumentException 
     */
    public static function create($options)
    {
        if (is_array($options) && isset($options['key']) && isset($options['value'])) {
            return new static($options['key'], $options['value'], $options['type'] ?? 1);
        }
        if (is_array($options) && (count($options) >= 2) && ($options === array_filter($options, 'is_scalar'))) {
            return new static(...array_values($options));
        }
        if (is_array($options) && count($options) === 1 && (($options[0] ?? null) instanceof self)) {
            return $options[0]->copy();
        }
        if (!($options instanceof HTTPResponseRequestOption)) {
            throw new InvalidArgumentException(__METHOD__ . ' expect an insance of ' . __CLASS__ . ' or a PHP array as parameter, got ' . (null !== $options && is_object($options) ? get_class($options) : gettype($options)));
        }
        return $options->copy();
    }

    /**
     * Creates a new instance of the current class by copying attrributes values from the provided object
     * 
     * @param HTTPResponseRequestOption $option 
     * @return static 
     */
    private function copy()
    {
        return new static($this->getKey(), $this->getValue(), $this->getType() ?? 1);
    }

    /**
     * Return the response request option key
     * 
     * @return string 
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Return the response request option value
     * 
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Return the response request option type
     * 
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    public function toArray()
    {
        return [
            'key' => $this->getKey(),
            'value' => $this->getValue(),
            'type' => $this->getType()
        ];
    }
}
