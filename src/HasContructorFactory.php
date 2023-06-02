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

trait HasContructorFactory
{
    /**
     * Creates an instance of the current class.
     *
     * @param mixed $args
     *
     * @return self
     */
    public static function new(...$args)
    {
        return new static(...$args);
    }
}
