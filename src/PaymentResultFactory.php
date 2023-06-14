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

class PaymentResultFactory implements PaymentResultFactoryInterface
{
    /**
     * @var HTTPResponseConfigInterface
     */
    private $responseConfig;

    /**
     * Creates new class instance.
     *
     * @return void
     */
    public function __construct(HTTPResponseConfigInterface $responseConfig = null)
    {
        $this->responseConfig = $responseConfig ?? HTTPResponseConfig::create();
    }

    public function createPaymentResult($object): PaymentResult
    {
        $object = \is_array($object) ? $object : (\is_object($object) ? get_object_vars($object) : []);

        $paymentResult = new PaymentResult();

        if (null !== ($ref = $this->arrayGet($object, $this->responseConfig->getTxnReferenceKey()))) {
            $paymentResult = $paymentResult->withTxnReference(strval($ref));
        }
        if (null !== ($txnTime = $this->arrayGet($object, $this->responseConfig->getTxnTimeKey()))) {
            $paymentResult = $paymentResult->withTxnTime((string)$txnTime);
        }
        if (null !== ($txnAmount = $this->arrayGet($object, $this->responseConfig->getTxnAmountKey(), 0.0))) {
            $paymentResult = $paymentResult->withTxnAmount($txnAmount);
        }
        if (null !== ($txnId = $this->arrayGet($object, $this->responseConfig->getTxnIdKey()))) {
            $paymentResult = $paymentResult->withTxnId((string)$txnId);
        }
        if (null !== ($processorTxnId = $this->arrayGet($object, $this->responseConfig->getTxnProcessorKey()))) {
            $paymentResult = $paymentResult->withProcessorTxnId((string)$processorTxnId);
        }
        if ((null !== $this->responseConfig->getTxnPayeerIdKey()) && null !== ($payeerId = $this->arrayGet($object, $this->responseConfig->getTxnPayeerIdKey()))) {
            $paymentResult = $paymentResult->withTxnPayeerId((string)$payeerId);
        }
        return $paymentResult;
    }

    /**
     * Query for value matching the `$name` variable.
     *
     * @param mixed $default
     *
     * @return mixed
     */
    private function arrayGet(array $array, string $name = null, $default = null)
    {
        if (false !== strpos($name, '.')) {
            $keys = explode('.', $name);
            $count = \count($keys);
            $index = 0;
            $current = $array;
            while ($index < $count) {
                // code...
                if (null === $current) {
                    return $default;
                }
                $current = \array_key_exists($keys[$index], $current) ? $current[$keys[$index]] : $current[$keys[$index]] ?? null;
                ++$index;
            }

            return $current;
        }

        return \array_key_exists($name, $array ?? []) ? $array[$name] : $default;
    }
}
