<?php

namespace Drewlabs\TxnClient;

use JsonException;

class JSONDecoder
{
    /**
     * 
     * @var bool
     */
    private $associative;

    /**
     * 
     * @var int
     */
    private $depth = 512;

    /**
     * 
     * @var int
     */
    private $flags = JSON_THROW_ON_ERROR;

    /**
     * Creates a {@see \Drewlabs\TxnClient\JSONDecoder} instance
     * 
     * @param bool $associative 
     * @param null|int $depth 
     * @param int $flags 
     */
    public function __construct($associative = true, ?int $depth = null, int $flags)
    {
        $this->associative = $associative;
        $this->depth = $depth ?? 512;
        $this->flags =  $flags ?? JSON_THROW_ON_ERROR;
    }

    /**
     * Decode a JSON string to a PHP array (dictionnary) or a PHP object
     * 
     * @param string $value 
     * 
     * @throws JsonException
     * 
     * @return object|array 
     */
    public function decode(string $value)
    {
        return @json_decode($value, $this->associative, $this->depth, $this->flags);
    }
}
