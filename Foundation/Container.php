<?php

abstract class Container
{
    /**
     * @var array
     */
    protected static $collection = array();

    /**
     * Seta um valor
     *
     * @param string $key
     * @param mixed $value
     *
     * @static
     * @return void
     */
    public static function set($key, $value)
    {
        if($value instanceof \Closure) {
            $value = call_user_func($value);
        }

        self::$collection[$key] = $value;
    }

    /**
     * Obtêm um valor
     *
     * @param string $key
     *
     * @static
     * @return mixed
     */
    public static function get($key)
    {
        return self::$collection[$key];
    }
}
