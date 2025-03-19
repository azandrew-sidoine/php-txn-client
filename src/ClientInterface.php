<?php

namespace Drewlabs\TxnClient;

use InvalidArgumentException;
use ReflectionException;
use UnexpectedValueException;
use RuntimeException;

interface ClientInterface
{
    /**
     * Send a request to the Txn Gateway web service and return a Txn instance to the client.
     * 
     * **Note**
     * If the $request parameter is a string, parameters like $amount and processors are required else an 
     * InvalidArgumentException is thrown by the method
     * 
     * @param TxnRequestInterface|TxnInterface|TxnRequestBodyInterface|string $request Txn Instance or Txn Rrequest instance or an invoice reference
     * @param float|null $amount                                                       Txn amount 
     * @param array|null $processors                                                   List of processors that will handle the tnx
     * @param string $currency                                                         The currency in which the Txn is being paid
     * @param string|null $label                                                       Txn label used as title for the payment page
     * @param string|null $debtor                                                      Label given to the entity who is paying the Txn
     * @return TxnInterface 
     * 
     * @throws InvalidArgumentException
     * @throws TxnRequestException
     */
    public function createTxn(
        $request,
        ?float $amount = null,
        ?array $processors = null,
        $currency = 'XOF',
        ?string $label = null,
        ?string $debtor = null,
    );

    /**
     * Send the Invoice transaction request
     * 
     * @param TxnRequestInterface $request 
     * @return TxnInterface 
     * @throws RuntimeException 
     * @throws TxnRequestException 
     * @throws InvalidArgumentException 
     * @throws ReflectionException 
     * @throws UnexpectedValueException 
     */
    public function sendRequest(TxnRequestInterface $request);
}
