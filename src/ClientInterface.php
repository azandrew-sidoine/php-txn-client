<?php

namespace Drewlabs\TxnClient;

interface ClientInterface
{
    /**
     * Send a request to the Txn Gateway web service and return 
     * a Txn instance to the client.
     * 
     * @param TxnRequestInterface $request
     * 
     * @return TxnInterface 
     */
    public function request(TxnRequestInterface $request);
}