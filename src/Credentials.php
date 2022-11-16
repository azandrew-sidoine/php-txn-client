<?php

namespace Drewlabs\TxnClient;

class Credentials
{
    /**
     * Authentication key for authorizing request
     * 
     * @var string
     */
    private $key;

    /**
     * Authentication secret for authorizing request
     * 
     * @var string
     */
    private $secret;

    /**
     * Creates {@see \Drewlabs\TxnClient\Credentials} instance
     * 
     * @param string|null $key 
     * @param string $secret 
     * @return void 
     */
    public function __construct($key, string $secret)
    {
        $this->key = $key;
        $this->secret = $secret;
    }

    /**
     * Authorization secret property getter method
     * 
     * @return string 
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Authorization secret property getter method
     * 
     * @return string 
     */
    public function getSecret()
    {
        return $this->secret;
    }
}
