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

class HTTPResponseRequestMetadataType
{
    /**
     * HTTP request metadata is passed as HTTP header.
     *
     * @var int
     */
    public const HEADER = 1;

    /**
     * HTTP request metadata is passed in HTTP request body.
     *
     * @var int
     */
    public const BODY = 2;

    /**
     * @var int[]
     */
    public const VALUES = [self::HEADER, self::BODY];
}
