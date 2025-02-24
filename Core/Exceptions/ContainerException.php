<?php

namespace Core\Exceptions;

use Exception;
use Psr\Container\ContainerExceptionInterface;

class ContainerException extends Exception implements ContainerExceptionInterface
{
    /**
     * Generic exception in a container.
     */
}