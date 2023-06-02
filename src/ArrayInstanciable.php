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

trait ArrayInstanciable
{
    /**
     * Create an instance of the class and set attribute from setter method or property name.
     *
     * @throws \ReflectionException
     *
     * @return object
     */
    public static function createFromArray(array $attributes = [])
    {
        /**
         * @var object
         */
        $instance = (new \ReflectionClass(__CLASS__))->newInstanceWithoutConstructor();
        foreach ($attributes as $name => $value) {
            if (null === $value) {
                continue;
            }
            // Tries to generate a camelcase method name from property name and prefix it with set
            $method = 'set'.str_replace(' ', '', ucwords(str_replace('_', ' ', $name)));
            if (method_exists($instance, $method)) {
                \call_user_func([$instance, $method], $value);
                continue;
            }
            if (property_exists($instance, $name)) {
                $instance->{$name} = $value;
                continue;
            }
        }

        return $instance;
    }
}
