<?php

namespace Core;

use Core\Container\Container;

class App
{
    /**
     * Facade pattern implementation
     * Class intended for convenient work with containers
     */

    protected static Container $container;

    /**
     * @param Container $container
     * @return void
     */
    public static function setContainer(Container $container): void
    {
        static::$container = $container;
    }

    /**
     * @return Container
     */
    public static function getContainer(): Container
    {
        return static::$container;
    }

    /**
     * @param string $id
     * @param string|callable $resolver
     * @return void
     */
    public static function bind(string $id, string|callable $resolver): void
    {
        static::getContainer()->bind($id, $resolver);
    }

    /**
     * @param string $id
     * @param object $instance
     * @return void
     */
    public static function singleton(string $id, object $instance): void
    {
        static::getContainer()->singleton($id, $instance);
    }

    /**
     * @throws Exceptions\ContainerException
     * @throws Exceptions\ContainerNotFoundException
     */
    public static function get($id)
    {
        return static::getContainer()->get($id);
    }
}