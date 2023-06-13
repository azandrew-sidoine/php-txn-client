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
     * Creates new class instance
     * 
     * @param HTTPResponseConfigInterface|null $responseConfig 
     * @return void 
     */
    public function __construct(HTTPResponseConfigInterface $responseConfig = null)
    {
        $this->responseConfig = $responseConfig ?? HTTPResponseConfig::create();
    }

    public function createPaymentResult($object): PaymentResult
    {
        $object = is_array($object) ? $object : (is_object($object) ? get_object_vars($object) : []);
        return (new PaymentResult)
            ->setTxnReference($this->arrayGet($object, $this->responseConfig->getTxnReferenceKey()))
            ->setTxnTime($this->arrayGet($object, $this->responseConfig->getTxnTimeKey()))
            ->setTxnAmount($this->arrayGet($object, $this->responseConfig->getTxnAmountKey(), 0.0))
            ->setTxnId($this->arrayGet($object, $this->responseConfig->getTxnIdKey()))
            ->setProcessorTxnId($this->arrayGet($object, $this->responseConfig->getTxnProcessorKey()))
            ->setTxnPayeerid($this->arrayGet($object, $this->responseConfig->getTxnPayeerIdKey()));
    }

    /**
     * Query for value matching the `$name` variable
     * 
     * @param array $array 
     * @param string $name 
     * @param mixed $default 
     * @return mixed 
     */
    private function arrayGet(array $array, string $name, $default = null)
    {

		if (false !== strpos($name, '.')) {
			$keys = explode('.', $name);
			$count = count($keys);
			$index = 0;
			$current = $array;
			while ($index < $count) {
				# code...
				if (null === $current) {
					return $default;
				}
				$current = array_key_exists($keys[$index], $current) ? $current[$keys[$index]] : $current[$keys[$index]] ?? null;
				$index += 1;
			}
			return $current;
		}
		return array_key_exists($name, $array ?? []) ? $array[$name] : $default;
    }
}
