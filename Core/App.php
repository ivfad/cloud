<?php

namespace Core;

class App
{
    protected static Container $container;

    public static function setContainer(Container $container): void
    {
        static::$container = $container;
    }

    public static function getContainer(): Container
    {
        return static::$container;
    }

    public static function bind(string $id, string|callable $resolver): void
    {
        static::getContainer()->bind($id, $resolver);
    }

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