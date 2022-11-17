<?php

namespace Drewlabs\TxnClient\Converters;

use Drewlabs\TxnClient\HasContructorFactory;

/**
 * @method static JSONEncoder new(?int $depth = null, int $flags)
 * 
 * @package Drewlabs\TxnClient
 */
class JSONEncoder
{
    use HasContructorFactory;

    /**
     * 
     * @var int
     */
    private $depth = 512;

    /**
     * 
     * @var int
     */
    private $flags = JSON_PRETTY_PRINT;

    /**
     * Creates a {@see \Drewlabs\TxnClient\JSONEncoder} instance
     * 
     * @param null|int $depth 
     * @param int $flags 
     */
    public function __construct(?int $depth = null, int $flags = 0)
    {
        $this->depth = $depth ?? 512;
        $this->flags =  $flags ?? JSON_PRETTY_PRINT;
    }


    /**
     * Encode a PHP json serializable type to a JSON string
     * 
     * @param mixed $value
     * 
     * @return string|false 
     */
    public function encode($value)
    {
        return @json_encode($value, $this->flags, $this->depth);
    }
}
