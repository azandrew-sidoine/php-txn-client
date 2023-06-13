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

interface HTTPResponseRequestMetadataInterface extends Arrayable
{
    /**
     * Return the response request option key.
     *
     * @return string
     */
    public function getKey();

    /**
     * Return the response request option value.
     *
     * @return string
     */
    public function getValue();

    /**
     * Return the response request option type.
     *
     * @return string
     */
    public function getType();

    /**
     * Clone the current object
     * 
     * @return static 
     */
    public function clone();
}
