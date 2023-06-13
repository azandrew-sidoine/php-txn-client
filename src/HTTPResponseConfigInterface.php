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
     * Get the txn_reference_key property.
     *
     * @return string
     */
    public function getTxnReferenceKey();

    /**
     * Get the txn_time_key property.
     *
     * @return string
     */
    public function getTxnTimeKey();

    /**
     * Get the txn_amount_key property.
     *
     * @return string
     */
    public function getTxnAmountKey();

    /**
     * Get the txn_id_key property.
     *
     * @return string
     */
    public function getTxnIdKey();

    /**
     * Get the txn_processor_key property.
     *
     * @return string
     */
    public function getTxnProcessorKey();

    /**
     * get `txn_payeer_id_key` property value
     * 
     * @return string 
     */
    public function getTxnPayeerIdKey();

    /**
     * Get the Http response request options.
     *
     * @return HTTPResponseRequestOption[]
     */
    public function getRequestOptions();
}
