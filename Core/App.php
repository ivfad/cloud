<?php

namespace Core;

use Core\Container\Container;
use Psr\Container\ContainerExceptionInterface;

class App
{
    /**
     * Facade pattern implementation
     * Class intended for convenient work with container bindings and
     */

    protected static Container $container;

    /**
     * Bind an instance to the container
     * @param string $id
     * @param string|callable $resolver
     * @return void
     */
    public static function bind(string $id, string|callable $resolver): void
    {
        static::getContainer()->bind($id, $resolver);
    }

    /**
     * Getter of the container instance (singleton)
     * @return Container
     */
    public static function getContainer(): Container
    {
        return static::$container;
    }

    /**
     * Setter of the container instance (singleton)
     * @param Container $container
     * @return void
     */
    public static function setContainer(Container $container): void
    {
        static::$container = $container;
    }

    /**
     * Bind a singleton-type instance to the container
     * @param string $id
     * @param object $instance
     * @return void
     */
    public static function singleton(string $id, object $instance): void
    {
        static::getContainer()->singleton($id, $instance);
    }

    /**
     * Gets a bound instance (of any type) from the container
     * @param string $id
     * @return mixed
     * @throws ContainerExceptionInterface
     */
    public static function get(string $id): mixed
    {
        return static::getContainer()->get($id);
    }
}