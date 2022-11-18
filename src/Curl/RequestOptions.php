<?php

namespace Drewlabs\Curl;

use ReflectionClass;

class RequestOptions
{
    /**
     * 
     * @var array
     */
    private $headers = [];

    /**
     * 
     * @var array
     */
    private $auth;

    /**
     * 
     * @var array|string|\JsonSerializable|object
     */
    private $body;

    /**
     * 
     * @var boolean|string
     */
    private $compress;

    /**
     * 
     * @var array|object
     */
    private $query;

    /**
     * 
     * @var string
     */
    private $encoding;

    /**
     * Creates a request options object
     * 
     * @param array $headers 
     * @param array $body 
     * @param array $query 
     * @return void 
     */
    public function __construct(array $headers = [], $body = [], $query = [])
    {
        $this->headers = $headers;
        $this->body = $body;
        $this->query = $query;
    }
    /**
     * Create a new {@see \Drewlabs\Curl\ClientOptions} instance
     * 
     * **Note**
     * 
     * ```php
     * $requestOptions = RequestOptions::create([
     *      'headers' => [
     *          'Content-Type': 'multipart/form-dat',
     *      ],
     *      'body' => [
     *          // Request body ...
     *      ],
     *      'query' => [
     *          // Request query parameters ...
     *      ],
     *      'auth' => ['user', 'pass', 'basic'] // the 3rd option is optional. Defaults to basic,
     *      'compress' => true
     * ]);
     * ```
     * 
     * @param array $properties 
     * @return static 
     * @throws ReflectionException 
     */
    public static function create(array $properties = null)
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
     * Set the headers options
     * 
     * @param array $headers 
     * @return static 
     */
    public function setHeaders(array $headers)
    {
        $this->headers = $headers;
        return $this;
    }

    /**
     * Get the headers options
     * 
     * @return array 
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set the request body options
     * 
     * @param array|string|\JsonSerializable|object $body
     * @return static 
     */
    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    /**
     * Set the request body with which default request is overriden
     * 
     * @return mixed 
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set the authentication options
     * 
     * @param string $user 
     * @param string $password 
     * @param string $type 
     * @return void 
     */
    public function setAuth(string $user, string $password, $type = 'basic')
    {
        $this->auth = [$user, $password, $type];
        return $this;
    }

    /**
     * Get the authentication options
     * 
     * @return array 
     */
    public function getAuth()
    {
        return $this->auth ?? [];
    }

    /**
     * Set the compress option on the request. If true default compression
     * gzip,defalte will be used, else, the compression algoright passed
     * as parameter is used
     * 
     * @param mixed $value 
     * @return void 
     */
    public function setCompress($value)
    {
        $this->compress = $value;
    }

    /**
     * Get the request compress options
     * 
     * @return bool|string 
     */
    public function getCompress()
    {
        return $this->compress;
    }

    /**
     * Set the request query parameter. This query parameter will override
     * the default query used in the psr7 request
     * 
     * @param array $value 
     * @return void 
     */
    public function setQuery(array $value)
    {
        $this->query = $value;
    }

    /**
     * Returns the request query parameter to use to append to the request
     * url by the request client
     * 
     * @return array|object 
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * Set the content encoding request option
     * 
     * @param string $value 
     * @return static 
     */
    public function setEncoding(string $value)
    {
        $this->encoding = $value;
        return $this;
    }

    /**
     * Get the content encoding request option
     * 
     * @return string 
     */
    public function getEncoding()
    {
        return $this->encoding;
    }
}
