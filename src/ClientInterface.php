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

use InvalidArgumentException;

interface ClientInterface
{
    /**
     * Send a request to the Txn Gateway web service and return a Txn instance to the client.
     *
     * **Note**
     * If the $request parameter is a string, parameters like $amount and processors are required else an
     * InvalidArgumentException is thrown by the method
     *
     * @param TxnRequestInterface|TxnInterface|TxnRequestBodyInterface|string $request    Txn Instance or Txn Rrequest instance or an invoice reference
     * @param float|null                                                      $amount     Txn amount
     * @param array|null                                                      $processors List of processors that will handle the tnx
     * @param string                                                          $currency   The currency in which the Txn is being paid
     * @param string|null                                                     $label      Txn label used as title for the payment page
     * @param string|null                                                     $debtor     Label given to the entity who is paying the Txn
     *
     * @throws \InvalidArgumentException
     * @throws TxnRequestException
     *
     * @return TxnInterface
     */
    public function createTxn(
        $request,
        float $amount = null,
        array $processors = null,
        $currency = 'XOF',
        string $label = null,
        string $debtor = null,
    );

    /**
     * Send the Invoice transaction request.
     *
     * @throws \RuntimeException
     * @throws TxnRequestException
     * @throws \InvalidArgumentException
     * @throws \ReflectionException
     * @throws \UnexpectedValueException
     *
     * @return TxnInterface
     */
    public function sendRequest(TxnRequestInterface $request);
}
