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

interface PaymentResultFactoryInterface
{
    /**
     * Creates a payment result instance
     * 
     * @param array|object $object
     * 
     * @return PaymentResult 
     */
    public function create($object): PaymentResult;
}