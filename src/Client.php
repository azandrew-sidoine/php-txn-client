<?php

namespace Drewlabs\TxnClient;

use Drewlabs\Curl\Client as CURLClient;

final class Client implements ClientInterface
{

    /**
     * 
     * @var CURLClient
     */
    private $backend;


    /**
     * Creates an instance of {@see Drewlabs\TxnClient\Client} class
     */
    public function __construct()
    {
        $this->backend = new CURLClient;
    }

    public function request(TxnRequestInterface $request)
    {
        // TODO : Provide request handler implementation
        $this->prepare($request);
        $this->backend->execute($request->getBody());
    }

    private function prepare(TxnRequestInterface $request)
    {
        if ($uri = $request->getUri()) {
            $this->backend->setRequestUri($uri);
        }
        if ($method = $request->getMethod()) {
            $this->backend->setRequestMethod($method);
        }
        // Set the HTTP version
        $this->backend->setProtocolVersion($request->getProtocolVersion());
        // TODO : Disable SSL verification
        $this->backend->disableSSLVerification();
    }
}
