<?php

namespace Drewlabs\TxnClient;

use ReflectionException;

class HTTPResponseConfig implements HTTPResponseConfigInterface
{
    use ArrayInstanciable;

    /**
     * 
     * @var string
     */
    private $url;

    /**
     * 
     * @var string
     */
    private $redirect_response_url;

    /**
     * 
     * @var string
     */
    private $method;

    /**
     * 
     * @var string
     */
    private $txn_reference_key;

    /**
     * 
     * @var string
     */
    private $txn_time_key;

    /**
     * 
     * @var string
     */
    private $txn_amount_key;

    /**
     * 
     * @var string
     */
    private $txn_id_key;

    /**
     * 
     * @var string
     */
    private $txn_processor_key;

    /**
     * 
     * @var HTTPResponseRequestOption[]
     */
    private $request_options;

    public function __construct()
    {
    }


    /**
     * Create a response configuration instance from attributes array
     * 
     * ```php
     * 
     * <?php
     * 
     * use namespace Drewlabs\TxnClient\HTTPResponseConfig;
     * 
     * $config = HTTPResponseConfig::create(
     *      [
     *          'method' => 'GET',
     *          'txn_reference_key' => 't_ref',
     *          'txn_time_key' => 't_time',
     *          'txn_amount_key' => 't_montant',
     *          'txn_id_key' => 't_id',
     *          'txn_processor_key' => 't_processor_id',
     *          'request_options' => [
     *              // Send the response request option as array
     *              ['api_key', 'api_key_value', 1],
     *              // or using a PHP dictionary
     *              [
     *                  'key' => 'api_key',
     *                  'value' => 'api_key_value',
     *                  'type' => 1
     *              ]
     *          ]
     *      ]
     * );
     * ```
     * @param array $attributes 
     * @return static 
     * @throws ReflectionException 
     */
    public static function create(array $attributes = [])
    {
        $attributes = array_merge(static::defaults() ?? [], $attributes);
        if (is_array($attributes)) {
            return self::createFromArray($attributes);
        }
        return new static();
    }

    /**
     * Returns the default http response configuration array
     * 
     * @return string[] 
     */
    public static function defaults()
    {
        return [
            'method' => 'POST',
            'txn_reference_key' => 't_ref',
            'txn_time_key' => 't_time',
            'txn_amount_key' => 't_montant',
            'txn_id_key' => 't_id',
            'txn_processor_key' => 't_processor_id',
        ];
    }

    /**
     * Set the url property
     * 
     * @param string $value 
     * @return $this 
     */
    public function setUrl(string $value)
    {
        $this->url = $value;
        return $this;
    }

    /**
     * Set the redirect response url property
     * 
     * @param string $value 
     * @return $this 
     */
    public function setRedirectResponseUrl(string $value)
    {
        $this->redirect_response_url = $value;
        return $this;
    }

    /**
     * Set the method property
     * 
     * @param string $value 
     * @return $this 
     */
    public function setMethod(string $value)
    {
        $this->method = $value;
        return $this;
    }

    /**
     * Set the txn reference key property
     * 
     * @param string $value 
     * @return $this 
     */
    public function setTxnReferenceKey(string $value)
    {
        $this->txn_reference_key = $value;
        return $this;
    }

    /**
     * Set the txn time key property
     * 
     * @param string $value 
     * @return $this 
     */
    public function setTxnTimeKey(string $value)
    {
        $this->txn_time_key = $value;
        return $this;
    }

    /**
     * Set the txn amount key property
     * 
     * @param string $value 
     * @return $this 
     */
    public function setTxnAmountKey(string $value)
    {
        $this->txn_amount_key = $value;
        return $this;
    }

    /**
     * Set the txn id key property
     * 
     * @param string $value 
     * @return $this 
     */
    public function setTxnIdKey(string $value)
    {
        $this->txn_id_key = $value;
        return $this;
    }

    /**
     * Set the txn processor  property
     * 
     * @param string $value 
     * @return $this 
     */
    public function setTxnProcessorKey(string $value)
    {
        $this->txn_processor_key = $value;
        return $this;
    }

    /**
     * Set the Http response request options
     * 
     * @param array<array>|Arrayable $value 
     * @return void 
     */
    public function setRequestOptions($value)
    {
        $value = is_array($value) ? $value : [$value];
        $isArrayList = $value === array_filter($value, 'is_array');
        $value = $isArrayList ? $value : [$value];
        $this->request_options = array_map(function ($option) {
            return !($option instanceof Arrayable) ? HTTPResponseRequestOption::create($option) : $option;
        }, $value);
        return $this;
    }

    /**
     * Get the url property
     * 
     * @param string $value 
     * @return $this 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get the redirect_response_url property
     * 
     * @param string $value 
     * @return $this 
     */
    public function getRedirectResponseUrl()
    {
        return $this->redirect_response_url;
    }

    /**
     * Get the method property
     * 
     * @param string $value 
     * @return $this 
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Get the txn_reference_key property
     * 
     * @param string $value 
     * @return $this 
     */
    public function getTxnReferenceKey()
    {
        return $this->txn_reference_key;
    }

    /**
     * Get the txn_time_key property
     * 
     * @param string $value 
     * @return $this 
     */
    public function getTxnTimeKey()
    {
        return $this->txn_time_key;
    }

    /**
     * Get the txn_amount_key property
     * 
     * @param string $value 
     * @return $this 
     */
    public function getTxnAmountKey()
    {
        return $this->txn_amount_key;
    }

    /**
     * Get the txn_id_key property
     * 
     * @param string $value 
     * @return $this 
     */
    public function getTxnIdKey()
    {
        return $this->txn_id_key;
    }

    /**
     * Get the txn_processor_key property
     * 
     * @param string $value 
     * @return $this 
     */
    public function getTxnProcessorKey()
    {
        return $this->txn_processor_key;
    }

    /**
     * Get the Http response request options
     * 
     * @return HTTPResponseRequestOption[]
     */
    public function getRequestOptions()
    {
        return $this->request_options ?? [];
    }

    public function toArray()
    {
        return [
            'url' => $this->getUrl(),
            'redirect_response_url' => $this->getRedirectResponseUrl(),
            'method' => $this->getMethod(),
            't_ref_key' => $this->getTxnReferenceKey(),
            't_time_key' => $this->getTxnTimeKey(),
            't_amount_key' => $this->getTxnAmountKey(),
            't_id_key' => $this->getTxnIdKey(),
            't_processor_id_key' => $this->getTxnProcessorKey(),
            'options' => array_map(function (HTTPResponseRequestOption $option) {
                return $option->toArray();
            }, $this->getRequestOptions())
        ];
    }
}
