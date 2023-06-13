<?php

declare(strict_types=1);

/*
 * This file is part of the drewlabs namespace.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\TxnClient;

class Txn implements TxnInterface
{
    use ArrayInstanciable;

    /**
     * Txn payment url property.
     *
     * @var string
     */
    private $payment_url;

    /**
     * Txn id property.
     *
     * @var string
     */
    private $id;

    /**
     * Txn reference property.
     *
     * @var string
     */
    private $reference;

    /**
     * Txn amount property.
     *
     * @var float
     */
    private $amount;

    /**
     * Txn currency property.
     *
     * @var string
     */
    private $currency;

    /**
     * Txn processors property.
     *
     * @var string[]
     */
    private $processors;

    /**
     * Txn label property.
     *
     * @var string|null
     */
    private $label;

    /**
     * Txn debtor property.
     *
     * @var string|null
     */
    private $debtor;

    /**
     * Creates an instance of {@see \Drewlabs\TxnClient\Txn} class.
     *
     * @param float|int  $amount
     * @param string[]   $processors
     * @param string     $currency
     * @param string     $label
     * @param string     $debtor
     * @param string|int $id
     * @param string     $payment_url
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
        $this->currency = $currency ?? 'XOF';
        $this->amount = $amount;
        $this->processors = array_map(static fn ($processor) => (string) $processor, $processors ?? []);
        $this->label = $label;
        $this->debtor = $debtor;
    }

    public function clone()
    {
        return clone $this;
    }

    /**
     * Creates {@see \Drewlabs\TxnClient\Txn} instance from json structure (dictionnary/object).
     *
     * @param object|array $attributes
     *
     * @return self
     */
    public static function create($attributes)
    {
        if (\is_object($attributes)) {
            $attributes = get_object_vars($attributes);
        }
        if (!\is_array($attributes)) {
            throw new \InvalidArgumentException('Expected PHP array or object type, got '.(\is_object($attributes) && null !== $attributes ? $attributes::class : \gettype($attributes)));
        }
        if (\is_array($attributes)) {
            return self::createFromArray($attributes);
        }
        throw new \UnexpectedValueException('Expect parameter of Txn::create() to be an array, ');
    }

    /**
     * Set the txn id property.
     *
     * @param string|int $value
     *
     * @return static
     */
    public function setId($value)
    {
        return $this->merge('id', $value);
    }

    /**
     * Returns the txn payment url.
     *
     * @return static
     */
    public function setPaymentUrl(string $url)
    {
        if (false === ($components = @parse_url($url))) {
            throw new \UnexpectedValueException('$url parameter must be a valid resource url');
        }
        if (!\in_array(strtolower($components['scheme'] ?? ''), ['http', 'https'], true)) {
            throw new \UnexpectedValueException('Payment URL must be a valid HTTP resource URL');
        }

        return $this->merge('payment_url', $url);
    }

    /**
     * Returns the transaction reference.
     *
     * @return static
     */
    public function setReference(string $ref)
    {
        return $this->merge('reference', $ref);
    }

    /**
     * Set the amount property.
     *
     * @return static
     */
    public function setAmount(float $amount)
    {
        return $this->merge('amount', $amount);
    }

    /**
     * Set the processors property.
     *
     * @return static
     */
    public function setProcessors(array $processors)
    {
        return $this->merge('processors', array_map(static fn ($processor) => (string) $processor, $processors ?? []));
    }

    /**
     * Set the currency property.
     *
     * @return static
     */
    public function setCurrency(string $value)
    {
        return $this->merge('currency', $value);
    }

    /**
     * Set the label property.
     *
     * @return static
     */
    public function setLabel(string $value)
    {
        return $this->merge('label', $value);
    }

    /**
     * Set the response config of the current request object.
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
     * Returns the txn id.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the txn payment url.
     *
     * @return string|\Stringable
     */
    public function getPaymentUrl()
    {
        return $this->payment_url;
    }

    /**
     * Returns the transaction reference.
     *
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * Get the amount property.
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Get the processors property.
     *
     * @return string[]
     */
    public function getProcessors()
    {
        return $this->processors;
    }

    /**
     * Get the currency property.
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Get the label property.
     *
     * @return string|null
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * Get the debtor property.
     *
     * @return string|null
     */
    public function getDebtor()
    {
        return $this->debtor;
    }

    /**
     * Merge the instance property to modify in the existing properties.
     *
     * @param mixed $value
     *
     * @return static
     */
    protected function merge(string $attribute, $value)
    {
        $object = $this->clone();
        $object->{$attribute} = $value;

        return $object;
    }
}
