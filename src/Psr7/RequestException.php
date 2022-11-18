<?php

namespace Drewlabs\Psr7;

use Exception;
use Psr\Http\Client\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;

class RequestException extends Exception implements RequestExceptionInterface
{
    /**
     * 
     * @var RequestInterface
     */
    private $request;

    public function __construct(RequestInterface $request, string $message, $errorcode)
    {
        parent::__construct($message, $errorcode);
        $this->request = $request;
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
