<?php

namespace Drewlabs\TxnClient;

use Drewlabs\Curl\Converters\JSONEncoder;

class TxnRequestBody implements TxnRequestBodyInterface
{
    /**
     * 
     * @var string
     */
    private $ref;

    /**
     * 
     * @var float
     */
    private $amount;

    /**
     * 
     * @var string[]
     */
    private $processors;

    /**
     * 
     * @var string
     */
    private $currency = 'XOF';

    /**
     * 
     * @var string|null
     */
    private $label;

    /**
     * 
     * @var string|null
     */
    private $debtor;

    /**
     * 
     * @var Arrayable
     */
    private $response;

    /**
     * Creates an instance of the {@see \Drewlabs\TxnClient\TxnRequestBody} class
     * 
     * @param string $reference 
     * @param mixed $amount 
     * @param array $processors 
     * @param string $currency 
     * @param string|null $label 
     * @param string|null $debtor 
     * @param Arrayable $response 
     * @return void 
     */
    public function __construct(
        string $reference,
        $amount,
        array $processors,
        string $currency = 'XOF',
        Arrayable $response = null,
        string $label = null,
        string $debtor = null
    ) {

        $this->ref = $reference;
        $this->amount = $amount;
        $this->processors = array_map(function ($processor) {
            return (string)$processor;
        }, $processors ?? []);
        $this->currency = $currency ?? 'XOF';
        $this->label = $label;
        $this->debtor = $debtor;
        $this->response = $response;
    }

    public function setCurrency(string $value)
    {
        return $this->merge('currency', $value);
    }

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
     * Set the response config of the current request object
     * 
     * @param Arrayable|array $value 
     * 
     * @return static 
     */
    public function setResponseConfig($value)
    {
        return $this->merge('response', is_array($value) ? HTTPResponseConfig::create($value) : $value);
    }

    public function toArray()
    {
        return [
            'reference' => $this->ref,
            'amount' => $this->amount,
            'currency' => $this->currency,
            'debtor' => $this->debtor,
            'invoice_label' => $this->label,
            'processors' => $this->processors,
            'http_response' => $this->response ? $this->response->toArray() : null
        ];
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
        $object = clone($this);
        $object->__set($attribute, $value);
        return $object;
    }

    /**
     * Returns the string representation of the current instance
     * 
     * @return string 
     */
    public function __toString()
    {
        return (new JSONEncoder)->encode($this->toArray());
    }
}
