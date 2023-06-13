<?php

/*
 * This file is part of the drewlabs namespace.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Drewlabs\TxnClient;

class AuthorizationException extends TxnRequestException
{
    /**
     * Creates class instance.
     *
     * @return void
     */
    public function __construct(TxnRequestInterface $request, array $headers = [], string $message = 'Unauthorized.')
    {
        parent::__construct($request, 401, $headers, $message);
    }
}
