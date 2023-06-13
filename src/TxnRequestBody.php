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

use Drewlabs\Curl\Converters\JSONEncoder;

class TxnRequestBody implements TxnRequestBodyInterface
{
    /**
     * @var TxnInterface
     */
    private $txn;

    /**
     * @var HTTPResponseConfigInterface
     */
    private $response;

    /**
     * Creates an instance of the {@see \Drewlabs\TxnClient\TxnRequestBody} class.
     *
     * @param TxnInterface                $txn
     * @param HTTPResponseConfigInterface $response
     */
    public function __construct(Txn $txn, HTTPResponseConfigInterface $response = null)
    {
        $this->txn = $txn;
        $this->response = $response;
    }

    /**
     * Returns the string representation of the current instance.
     *
     * @return string
     */
    public function __toString()
    {
        return (new JSONEncoder())->encode($this->toArray());
    }

    /**
     * Creates a copy of the current instance.
     *
     * @return void
     */
    public function __clone()
    {
        if ($this->txn) {
            $this->txn = $this->txn->clone();
        }
        if ($this->response) {
            $this->response = $this->response->clone();
        }
    }

    public function clone()
    {
        return clone $this;
    }

    /**
     * Set the response config of the current request object.
     *
     * @param Arrayable|array $value
     *
     * @return static
     */
    public function setResponseConfig($value)
    {
        $object = clone $this;
        $object->response = \is_array($value) ? HTTPResponseConfig::create($value) : $value;

        return $object;
    }

    /**
     * Set the txn property.
     *
     * @return static
     */
    public function setTxn(TxnInterface $txn)
    {
        $object = clone $this;
        $object->txn = $txn;

        return $object;
    }

    public function getResponseConfig()
    {
        return $this->response;
    }

    public function getTxn()
    {
        return $this->txn;
    }

    public function toArray()
    {
        if (empty($this->txn->getProcessors())) {
            throw new MalformedRequestException('No invoice processor provided !');
        }
        if (empty($this->txn->getReference())) {
            throw new MalformedRequestException('No invoice reference value provided !');
        }
        if (empty($this->txn->getAmount())) {
            throw new MalformedRequestException('No invoice amount value provided !');
        }

        return [
            'reference' => $this->txn->getReference(),
            'amount' => $this->txn->getAmount(),
            'currency' => $this->txn->getCurrency(),
            'debtor' => $this->txn->getDebtor(),
            'invoice_label' => $this->txn->getLabel(),
            'processors' => $this->txn->getProcessors(),
            'http_response' => $this->response ? $this->response->toArray() : null,
        ];
    }
}
