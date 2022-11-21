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
        $this->backend->send($request->getBody());
    }

    // /**
    //  * 
    //  * @param array $cookies 
    //  * @return void 
    //  */
    // private function setRequestCookies(array $cookies)
    // {
    //     if (count($cookies)) {
    //         $this->setOption(CURLOPT_COOKIE, implode('; ', array_map(function ($key, $value) {
    //             return $key . '=' . $value;
    //         }, array_keys($cookies), array_values($cookies))));
    //     }
    // }

    //     /**
    //  * Set the curl session headers
    //  * 
    //  * @param array $requestHeaders 
    //  * @return void 
    //  */
    // private function setRequestHeaders(array $requestHeaders = [])
    // {
    //     $headers = [];
    //     foreach ($requestHeaders as $key => $value) {
    //         $headers[] = $key . ': ' . implode(', ', $value);
    //     }
    //     $this->setOption(CURLOPT_HTTPHEADER, $headers);
    // }

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
