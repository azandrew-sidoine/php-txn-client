<?php

namespace Drewlabs\TxnClient;

class TxnBadRequestException extends TxnRequestException
{
    private $errors;

    /**
     * Creates a class exceptions
     * 
     * @param array $errors 
     * @param string $message 
     * @param int $code 
     */
    public function __construct(array $errors, string $message, int $code = 422)
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    /**
     * Set the bad request errors on the exception
     * 
     * @param array $errors 
     * @return static 
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;
        return $this;
    }

    /**
     * Returns the list of errors of http bad request 
     * 
     * @return array 
     */
    public function getErrors()
    {
        return $this->errors;
    }
}