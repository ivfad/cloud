<?php

namespace Core;

trait SingletonTrait
{
    /**
     * Singleton pattern implementation
     */
    final protected function __construct() {}

    final protected function __clone() {}

    final public function __wakeup() {}

    /**
     * @return static
     */
    final public static function getInstance():static
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new static();
        }

        return $instance;
    }
}