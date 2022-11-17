<?php

namespace Drewlabs\TxnClient;

use Drewlabs\TxnClient\Converters\JSONEncoder;

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

    public function __construct(
        string $reference,
        $amount,
        array $processors,
        string $currency = 'XOF',
        string $label = null,
        string $debtor = null,
        Arrayable $response
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

    public function currency(string $value)
    {
        return $this->merge('currency', $value);
    }

    public function label(string $value)
    {
        return $this->merge('label', $value);
    }

    public function debtor(string $value)
    {
        return $this->merge('debtor', $value);
    }

    public function responseConfig(Arrayable $value)
    {
        return $this->merge('response', $value);
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

    protected function merge(string $attribute, $value)
    {
        /**
         * @var object|\stdClass
         */
        $object = clone($this);
        $object->__set($attribute, $value);
        return $object;
    }

    public function __toString()
    {
        return JSONEncoder::new()->encode($this->toArray());
    }
}
