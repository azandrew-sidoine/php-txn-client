<?php

namespace Drewlabs\Curl;

use ReflectionClass;
use Psr\Http\Message\StreamInterface;

class ClientOptions
{
    /**
     * 
     * @var bool
     */
    private $verify;

    /**
     * 
     * @var bool
     */
    private $decode_content;

    /**
     * 
     * @var string
     */
    private $sink;

    /**
     * 
     * @var int
     */
    private $timeout;

    /**
     * 
     * @var bool
     */
    private $force_resolve_ip;

    /**
     * 
     * @var array
     */
    private $proxy;

    /**
     * 
     * @var array<string>
     */
    private $cert;

    /**
     * 
     * @var array<string>
     */
    private $ssl_key;

    /**
     * 
     * @var \Closure|callable
     */
    private $progress;

    /**
     * 
     * @var string
     */
    private $base_url;

    /**
     * 
     * @var int
     */
    private $connect_timeout;

    /**
     * Creates an instance of the clien options class
     * 
     * @param string $base_url
     * @param bool|null $verify 
     * @param int|null $timeout 
     * @param Closure $progress 
     * @param string|null $tmpWritePath 
     * @return void 
     */
    public function __construct(
        string $base_url = null,
        bool $verify = null,
        int $timeout = null,
        \Closure $progress = null,
        string $tmpWritePath = null
    ) {
        $this->base_url = $base_url;
        $this->verify = $verify;
        $this->timeout = $timeout;
        $this->progress = $progress;
        $this->sink = $tmpWritePath;
    }


    /**
     * Create a new {@see \Drewlabs\Curl\ClientOptions} instance
     * 
     * @param array $properties 
     * @return static 
     * @throws ReflectionException 
     */
    public static function new(array $properties = null)
    {
        if (is_array($properties)) {
            $instance = (new ReflectionClass(__CLASS__))->newInstanceWithoutConstructor();
            foreach ($properties as $name => $value) {
                if (property_exists($instance, $name)) {
                    $instance->{$name} = $value;
                }
            }
            return $instance;
        }

        return new static;
    }


    /**
     * Configuration value for whether the host ssl is verified
     * 
     * @param bool|null $value 
     * @return bool 
     */
    public function verify(bool $value = null)
    {
        if (null !== $value) {
            $this->verify = $value;
        }
        return $this->verify;
    }

    /**
     * Whether to decode the request body
     * 
     * @param bool|null $value 
     * @return bool 
     */
    public function decodeContent(bool $value = null)
    {
        if (null !== $value) {
            $this->decode_content = $value;
        }
        return $this->decode_content ?? false;
    }

    /**
     * The path where downloaded files are temporary written to
     * 
     * @param string|StreamInterface $value 
     * @return string 
     */
    public function sink($value = null)
    {
        if (null !== $value) {
            $this->sink = $value;
        }
        return $this->sink;
    }


    /**
     * Request timeout configuration value
     * 
     * @param int|null $value 
     * @return int|false 
     */
    public function timeout(int $value = null)
    {
        if (null !== $value) {
            $this->timeout = $value;
        }
        return $this->timeout;
    }

    /**
     * Force resolve host ip configuration value
     * 
     * @param bool|null $value 
     * @return bool 
     */
    public function forceResolveIp(bool $value = null)
    {
        if (null !== $value) {
            $this->force_resolve_ip = $value;
        }
        return $this->force_resolve_ip ?? false;
    }

    /**
     * Set the proxy configuration used by the client
     * 
     * @param mixed $value 
     * @return array 
     */
    public function proxy(string $proxy = null, $port = null, $username = null, $password = null)
    {
        if (null !== $proxy) {
            $this->proxy = func_get_args();
        }
        return $this->proxy ?? [];
    }

    /**
     * SSL Certificate password option.
     * 
     * ```php
     * $options = new ClientOptions();
     * 
     * $options->cert('path/to/cert', $password);
     * ``` 
     * 
     * @param string|null $value 
     * @param string|null $value 
     * @return array 
     */
    public function cert(string $path = null, string $password = null)
    {
        if (null !== $path) {
            $this->cert = [$path, $password];
        }
        return $this->cert ?? false;
    }

    /**
     * SSL key option.
     * 
     * ```php
     * $options = new ClientOptions();
     * 
     * $options->sslKey('path/to/cert', $password);
     * ``` 
     * 
     * @param string|null $value 
     * @param string|null $value 
     * @return array 
     */
    public function sslKey(string $path = null, string $password = null)
    {
        if (null !== $path) {
            $this->ssl_key = [$path, $password];
        }
        return $this->ssl_key ?? false;
    }

    /**
     * Set the lister of the progress event on the client
     * 
     * @param Closure|null $value 
     * @return callable|false 
     */
    public function progress(\Closure $value = null)
    {
        if (null !== $value) {
            $this->progress = $value;
        }
        return $this->progress ?? false;
    }

    /**
     * The Request client base url
     * 
     * @param string|null $value 
     * @return string 
     */
    public function baseURL(string $value = null)
    {
        if (null !== $value) {
            $this->base_url = $value;
        }
        return $this->base_url;
    }

    /**
     * Client Connection timeout
     * 
     * @param int|null $value 
     * @return int 
     */
    public function connectTimeout(int $value = null)
    { 
        if (null !== $value) {
            $this->connect_timeout = $value;
        }
        return $this->connect_timeout;
    }
}
