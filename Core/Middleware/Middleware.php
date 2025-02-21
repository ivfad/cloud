<?php

namespace Core\Middleware;

use Core\Exceptions\MiddlewareRoleException;
use JetBrains\PhpStorm\NoReturn;

class Middleware
{
    /**
     * Middleware layer, where the user's role is defined and then a handler of the corresponding class is applied
     */

    const ROLES = [
        'guest' => Guest::class,
        'user' => User::class,
        'admin' => Admin::class,
    ];


    /**
     * @param $role
     * @return void
     * @throws MiddlewareRoleException
     */
    #[NoReturn] public static function resolve($role): void
    {

        $middleware = static::ROLES[$role] ?? false;
        if(!$middleware) {
            throw new MiddlewareRoleException("No such middleware role: {$role}");
        }

        (new $middleware)->handle();
    }
}