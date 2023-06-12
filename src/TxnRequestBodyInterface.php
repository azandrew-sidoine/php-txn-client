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

interface TxnRequestBodyInterface extends Arrayable
{
    /**
     * Returns the string representation of the object.
     *
     * @return string
     */
    public function __toString();

    /**
     * Get the response config of the current request object.
     *
     * @return HTTPResponseConfigInterface
     */
    public function getResponseConfig();

    /**
     * Get the txn property.
     *
     * @return TxnInterface
     */
    public function getTxn();

    /**
     * Clone the current object
     * 
     * @return static 
     */
    public function clone();
}
