<?php

namespace Core\Helpers;

trait SingletonTrait
{
    /**
     * Singleton pattern implementation
     */
    final protected function __construct()
    {
    }

    /**
     * @return static
     */
    final public static function getInstance(): static
    {
        static $instance = null;

        if (is_null($instance)) {
            $instance = new static();
        }

        return $instance;
    }

    final public function __wakeup()
    {
    }

    final protected function __clone()
    {
    }
}