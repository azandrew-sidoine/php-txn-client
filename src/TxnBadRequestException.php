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

class TxnBadRequestException extends TxnRequestException
{
    private $errors;

    /**
     * Creates a class exceptions.
     */
    public function __construct(array $errors, string $message, int $code = 422)
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    /**
     * Set the bad request errors on the exception.
     *
     * @return static
     */
    public function setErrors(array $errors)
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * Returns the list of errors of http bad request.
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
