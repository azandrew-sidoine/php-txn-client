<?php

namespace Drewlabs\TxnClient;

use Drewlabs\TxnClient\TxnRequestException;

class AuthorizationException extends TxnRequestException
{
    /**
     * Creates class instance
     * 
     * @param TxnRequestInterface $request 
     * @param array $headers 
     * @param string $message 
     * @return void 
     */
    public function __construct(TxnRequestInterface $request, array $headers = [], string $message = 'Unauthorized.')
    {
        parent::__construct($request, 401, $headers, $message);
    }
}