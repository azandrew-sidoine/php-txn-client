<?php

declare(strict_types=1);

/*
 * This file is part of the drewlabs namespace.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\TxnClient;

use const CURLOPT_RETURNTRANSFER;

use Drewlabs\Curl\Client as CurlClient;
use Drewlabs\Curl\Converters\JSONDecoder;

use const FILTER_VALIDATE_URL;
use const PREG_SPLIT_NO_EMPTY;

final class Client implements ClientInterface
{
    /**
     * @var string
     */
    public const JSON_PATTERN = '/^(?:application|text)\/(?:[a-z]+(?:[\.-][0-9a-z]+){0,}[\+\.]|x-)?json(?:-[a-z]+)?/i';

    /**
     * @var CurlClient
     */
    private $curl;

    /**
     * @var string
     */
    private $txnRequestPath = 'api/transactions';

    /**
     * @var array
     */
    private $credentials = [];

    /**
     * @var string
     */
    private $responseURL;

    /**
     * @var string
     */
    private $redirectURL;

    /**
     * Creates an instance of {@see Drewlabs\TxnClient\Client} class.
     *
     * @param string $host
     * @param string $clientid
     * @param string $clientsecret
     */
    public function __construct(string $host = null, string $clientid = null, string $clientsecret = null)
    {
        $this->curl = new CURLClient($host);
        if ($clientsecret) {
            $this->credentials = ['clientid' => $clientid, 'clientsecret' => $clientsecret];
        }
    }

    /**
     * Set the client request credentials.
     *
     * @return static
     */
    public function setCredentials(string $secret, string $id = null)
    {
        $this->credentials = ['clientid' => $id, 'clientsecret' => $secret];

        return $this;
    }

    /**
     * Set the response endpoint and redirect endpoint for the current transaction.
     *
     * @param string      $url          HTTP Response endpoint URL
     * @param string|null $redirect_url HTTP Redirect endpoint URL
     *
     * @return static
     */
    public function respondTo(string $url, string $redirect_url = null)
    {
        $this->responseURL = $url;
        $this->redirectURL = $redirect_url;

        return $this;
    }

    public function createTxn(
        $request,
        float $amount = null,
        array $processors = null,
        $currency = 'XOF',
        string $label = null,
        string $debtor = null,
    ) {
        if (\is_string($request)) {
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
                $this->responseURL ? HTTPResponseConfig::create(
                    array_merge(HTTPResponseConfig::defaults() ?? [], [
                        'url' => $this->responseURL,
                        'redirect_response_url' => $this->redirectURL,
                    ])
                ) : null
            );
        }
        if ($request instanceof TxnRequestBodyInterface) {
            $request = new TxnRequest(
                $this->txnRequestPath,
                'POST',
                $request
            );
        }

        $txnRequest = null;
        if ($request instanceof TxnRequestInterface) {
            $txnRequest = $request;
        }

        if (null === $txnRequest) {
            throw new \UnexpectedValueException('Method parameters does not match the required parameters. Please check the method API definition for supported parameters');
        }
        if (($requestBody = $txnRequest->getBody())
            && null !== ($response = $requestBody->getResponseConfig())
            && ((null === $url = $response->getUrl()) || (false === filter_var($url, FILTER_VALIDATE_URL)))
        ) {
            throw new MalformedRequestException('Missing response url. Response url is required ');
        }

        return $this->sendRequest($txnRequest);
    }

    public function sendRequest(TxnRequestInterface $request)
    {
        $this->curl->setOption(CURLOPT_RETURNTRANSFER, true);

        if ($version = $request->getProtocolVersion()) {
            $this->curl->setProtocolVersion($version);
        }

        $this->curl->send($request->getMethod(), (string) $request->getUri(), [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => '*/*',
            ],
            'cookies' => !empty($this->credentials) ? $this->credentials : [],
            'body' => $request->getBody()->toArray(),
        ]);

        $response = $this->curl->getResponse();

        if (!empty($errorMessage = $this->curl->getErrorMessage()) || (0 !== $this->curl->getError())) {
            throw new TxnRequestException('Could not create invoice transaction'.($errorMessage ?? 'Unkkown error'));
        }

        $statusCode = $this->curl->getStatusCode();

        $responseHeaders = $this->parseHeaders($this->curl->getResponseHeaders());

        if (422 === (int) $statusCode || 400 === (int) $statusCode) {
            throw new TxnBadRequestException(
                $this->decodeRequestResponse($response, $responseHeaders),
                'Bad Http request'
            );
        }

        if ((int) $statusCode < 200 && 202 < (int) $statusCode) {
            throw new TxnRequestException(
                $errorMessage ?? class_exists(\Drewlabs\Psr7\ResponseReasonPhrase::class) ?
                    \call_user_func([\Drewlabs\Psr7\ResponseReasonPhrase::class, 'getPrase'], (int) $statusCode) :
                    'Unknown Request Error'
            );
        }
        $result = $this->decodeRequestResponse($response, $responseHeaders);
        if (
            !isset($result['reference'])
            && !isset($result['amount'])
            && !isset($result['id'])
            && !isset($result['paymenturl'])
        ) {
            throw new TxnRequestException('Txn response does not have the required attributes. Please contact the library author for more information about the issue');
        }

        return new Txn(
            $result['reference'],
            round(floatval($result['amount']), 2),
            [],
            $result['currency'] ?? null,
            $result['label'] ?? null,
            $result['debtor'] ?? null,
            $result['id'],
            $result['paymenturl']
        );
    }

    /**
     * Decode request response.
     *
     * @throws \JsonException
     *
     * @return array
     */
    private function decodeRequestResponse(string $response, array $headers = [])
    {
        $result = null;
        if (false !== preg_match(self::JSON_PATTERN, $this->getHeader($headers, 'content-type'))) {
            $result = (new JSONDecoder())->decode($response);
        }
        // If the Content-Type header is not present in the response headers, we apply the try catch clause
        // To insure no error is thrown when decoding.
        if (null === $result) {
            try {
                $result = (new JSONDecoder())->decode($response) ?? [];
            } catch (\Throwable $e) {
                $result = [];
            }
        }

        return (array) ($result ?? []);
    }

    /**
     * Parse request string headers.
     *
     * @param mixed $list
     *
     * @return array
     */
    private function parseHeaders($list)
    {
        $list = preg_split('/\r\n/', (string) ($list ?? ''), -1, PREG_SPLIT_NO_EMPTY);
        $httpHeaders = [];
        $httpHeaders['Request-Line'] = reset($list) ?? '';
        for ($i = 1; $i < \count($list); ++$i) {
            if (str_contains($list[$i], ':')) {
                [$key, $value] = array_map(static fn ($item) => $item ? trim($item) : null, explode(':', $list[$i], 2));
                $httpHeaders[$key] = $value;
            }
        }

        return $httpHeaders;
    }

    /**
     * Get request header caseless.
     *
     * @return string
     */
    private function getHeader(array $headers, string $name)
    {
        if (empty($headers)) {
            return null;
        }
        $normalized = strtolower($name);
        foreach ($headers as $key => $header) {
            if (strtolower($key) === $normalized) {
                return implode(',', \is_array($header) ? $header : [$header]);
            }
        }

        return null;
    }

    /**
     * Throws an exception if the amount and the processors parameters are null or not provided.
     *
     * @param mixed $amount
     * @param mixed $processors
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    private function assertCreateInvoiceRequiredParameters($amount, $processors)
    {
        if ((null === $amount) || (null === $processors) || !\is_array($processors)) {
            throw new \InvalidArgumentException('$amount and $processors parameters are required when parameter 1 is a string');
        }

        return true;
    }
}
