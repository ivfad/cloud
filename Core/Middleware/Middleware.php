<?php

namespace Core\Middleware;

class Middleware
{
    const ROLES = [
        'guest' => Guest::class,
        'user' => User::class,
        'admin' => Admin::class,
    ];

    public static function resolve($role)
    {
        if(!$role) {
            return;
        }
        $middleware = static::ROLES[$role] ?? false;

        if(!$middleware) {
            throw new \Exception("No such middleware role: {$role}");
        }

        return (new $middleware)->handle();
    }
}