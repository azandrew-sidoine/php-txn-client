<?php

namespace Drewlabs\TxnClient;

use Drewlabs\Curl\Client as CurlClient;
use InvalidArgumentException;
use UnexpectedValueException;

final class Client implements ClientInterface
{

    /**
     * 
     * @var CurlClient
     */
    private $curl;

    /**
     * 
     * @var string
     */
    private $txnRequestPath = 'api/transactions';

    /**
     * 
     * @var array
     */
    private $credentials = [];

    /**
     * Creates an instance of {@see Drewlabs\TxnClient\Client} class
     */

    /**
     * Creates an instance of {@see Drewlabs\TxnClient\Client} class
     * 
     * @param string $host 
     * @param string $clientid 
     * @param string $clientsecret 
     * @return void 
     */
    public function __construct(string $host = null, string $clientid = null, string $clientsecret = null)
    {
        $this->curl = new CURLClient(['url' => $host]);
        if ($clientsecret) {
            $this->credentials = ['clientid' => $clientid, 'clientsecret' => $clientsecret];
        }
    }

    /**
     * Set the client request credentials
     * 
     * @param string $secret 
     * @param string|null $id 
     * @return void 
     */
    public function setCredentials(string $secret, string $id = null)
    {
        $this->credentials = ['clientid' => $id, 'clientsecret' => $secret];
        return $this;
    }

    public function createInvoice(
        $request,
        float $amount = null,
        array $processors = null,
        $currency = 'XOF',
        string $label = null,
        string $debtor = null,
    ) {
        $tnxRequest = null;
        if (is_string($request)) {
            $this->assertCreateInvoiceRequiredParameters($amount, $processors);
            $request = new Txn(
                $request,
                $amount,
                $processors,
                $currency,
                $label,
                $debtor
            );
        }
        if ($request instanceof TxnInterface) {
            $request = new TxnRequestBody(
                $request,
                HTTPResponseConfig::create(HTTPResponseConfig::defaults())
            );
        }
        if ($request instanceof TxnRequestBodyInterface) {
            $tnxRequest = new TxnRequest(
                $this->txnRequestPath,
                'POST',
                $request
            );
        }

        if ($request instanceof TxnRequestInterface) {
            $tnxRequest = $request;
        }

        if (null === $tnxRequest) {
            throw new UnexpectedValueException('Method parameters does not match the required parameters. Please check the method API definition for supported parameters');
        }
        return $this->sendRequest($request);
    }

    public function sendRequest(TxnRequestInterface $request)
    {
        $this->curl->setOption(\CURLOPT_RETURNTRANSFER, true);
        if ($version = $request->getProtocolVersion()) {
            $this->curl->setProtocolVersion($version);
        }
        $this->curl->send($request->getMethod(), (string)$request->getUri(), [
            'headers' => [
                'Content-Type: application/json',
                'Accept: */*'
            ],
            'cookies' => !empty($this->credentials) ? $this->credentials : [],
            'body' => $request->getBody()->toArray()
        ]);
        $response = $this->curl->getResponse();
        if (!empty($message = $this->curl->getErrorMessage()) || (0 !== $this->curl->getError())) {
            throw new TxnRequestException('Could not create invoice transaction' . ($message ?? 'Unkkown error'));
        }
        print_r($response);
        die();
        // TODO: Parse the request response and create a txn instance
        return Txn::create($response);
    }

    /**
     * Throws an exception if the amount and the processors parameters are null or not provided
     * 
     * @param mixed $amount 
     * @param mixed $processors 
     * @return void 
     * @throws InvalidArgumentException 
     */
    private function assertCreateInvoiceRequiredParameters($amount, $processors)
    {
        if ((null === $amount) || (null === $processors) || !is_array($processors)) {
            throw new InvalidArgumentException('$amount and $processors parameters are required when parameter 1 is a string');
        }
        return true;
    }
}
