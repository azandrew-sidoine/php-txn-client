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

interface HTTPResponseConfigInterface extends Arrayable
{
    /**
     * Get the url property.
     *
     * @return $this
     */
    public function getUrl();

    /**
     * Clone the current object
     * 
     * @return static 
     */
    public function clone();

    /**
     * Get the Http response request options.
     *
     * @return HTTPResponseRequestOption[]
     */
    public function getRequestOptions();
}
