<?php

namespace Core\Exceptions;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

class ServiceNotFoundException extends Exception implements NotFoundExceptionInterface
{
    /**
     * No entry was found in the container.
     */
}