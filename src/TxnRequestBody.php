<?php

namespace Drewlabs\TxnClient;

use Drewlabs\Curl\Converters\JSONEncoder;

class TxnRequestBody implements TxnRequestBodyInterface
{
    /**
     * 
     * @var TxnInterface
     */
    private $txn;

    /**
     * 
     * @var Arrayable
     */
    private $response;

    /**
     * Creates an instance of the {@see \Drewlabs\TxnClient\TxnRequestBody} class
     * 
     * @param TxnInterface $txn
     * @param Arrayable $response 
     */
    public function __construct(Txn $txn, Arrayable $response = null)
    {
        $this->txn = $txn;
        $this->response = $response;
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
        $object = clone $this;
        $object->response = is_array($value) ? HTTPResponseConfig::create($value) : $value;
        return $object;
    }

    /**
     * Set the txn property
     * 
     * @param TxnInterface $txn 
     * @return static 
     */
    public function setTxn(TxnInterface $txn)
    {
        $object = clone $this;
        $object->txn = $txn;
        return $object;
    }

    public function getResponseConfig($value)
    {
        return $this->response;
    }

    public function getTxn()
    {
        return $this->txn;
    }

    public function toArray()
    {
        return [
            'reference' => $this->txn->getReference(),
            'amount' => $this->txn->getAmount(),
            'currency' => $this->txn->getCurrency(),
            'debtor' => $this->txn->getDebtor(),
            'invoice_label' => $this->txn->getLabel(),
            'processors' => $this->txn->getProcessors(),
            'http_response' => $this->response ? $this->response->toArray() : null
        ];
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

    /**
     * Creates a copy of the current instance
     * 
     * @return void 
     */
    public function __clone()
    {
        $this->txn = clone $this->txn;
        $this->response = clone $this->response;
    }
}
