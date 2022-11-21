<?php

namespace Drewlabs\TxnClient;

use ReflectionClass;
use ReflectionException;

trait ArrayInstanciable
{
    /**
     * Create an instance of the class and set attribute from setter method or property name
     * 
     * @param array $attributes 
     * @return object 
     * @throws ReflectionException 
     */
    public static function createFromArray(array $attributes = [])
    {
        $instance = (new ReflectionClass(__CLASS__))->newInstanceWithoutConstructor();
        foreach ($attributes as $name => $value) {
            if (null === $value) {
                continue;
            }
            // Tries to generate a camelcase method name from property name and prefix it with set
            if (method_exists($instance, $method = 'set' . str_replace(' ', '', ucwords(str_replace('_', ' ', $name))))) {
                call_user_func([$instance, $method], $value);
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
