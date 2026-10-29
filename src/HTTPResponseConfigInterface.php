<?php

namespace Drewlabs\TxnClient;

interface HTTPResponseConfigInterface extends Arrayable
{

    /**
     * Get the url property
     * 
     * @return $this 
     */
    public function getUrl();

    /**
     * Get the Http response request options
     * 
     * @return HTTPResponseRequestOption[]
     */
    public function getRequestOptions();

}