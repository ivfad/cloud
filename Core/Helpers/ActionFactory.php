<?php

namespace Core\Helpers;

use Closure;
use Core\Foundation\Controller;
use Exception;
use InvalidArgumentException;

class ActionFactory
{
    /**
     * Implementation of simple factory pattern.
     * Class used to handle closure-actions or array-actions.
     * For arrays checks if it matches the needed form [$controllerClassName, $action] and creates controllers instance.
     */

    /**
     * @throws Exception
     */
    public static function create(array|Closure $action): callable
    {
        if (is_callable($action)) {
            return $action;
        }

        if (is_array($action) && count($action) === 2) {
            [$controllerClassName, $action] = $action;

            if (!is_string($controllerClassName) || !is_string($action) || !class_exists($controllerClassName)) {
                throw new InvalidArgumentException("Invalid action provided.");
            }

            if (!is_subclass_of($controllerClassName, Controller::class)) {
                throw new InvalidArgumentException("'$controllerClassName' must be 'Controller' subclass");
            }

            $controllerObject = new $controllerClassName();

            return [$controllerObject, $action];
        }

        throw new InvalidArgumentException("Invalid action provided.");
    }
}