<?php

namespace Core\Container;

use Core\Exceptions\ContainerException;
use Core\Exceptions\ServiceNotFoundException;
use Core\Helpers\SingletonTrait;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;
use Throwable;

class Container implements ContainerInterface
{
    /**
     * IoC (Inversion of control) container implementation
     * The container manages the implementation of dependencies and acts as a layer
     * on which you can get them if necessary.
     */

    use SingletonTrait;

    /**
     * List of entries
     * @var array
     */
    protected array $bindings = [];

    /**
     * Associate a singleton-pattern element to bindings array
     * @param string $id example: ClassName::class
     * @param object $instance example: Object:getInstance()
     * @return $this
     */
    public function singleton(string $id, object $instance): Container
    {
        $this->bindings[$id] = $instance;

        return $this;
    }

    /**
     * Associate a common(non-singleton) element to bindings array
     * @param string $id
     * example#1.1: InterfaceName::class
     * example#1.2: ClassName::class
     * @param string|callable $resolver
     * example#2.1 (string): ClassName::class
     * ClassName::class should implement InterfaceName:class from example #1.1
     * example#2.2 (callable): function () {...}
     * @return $this
     */
    public function bind(string $id, string|callable $resolver): Container
    {
        $this->bindings[$id] = $resolver;

        return $this;
    }

    /**
     * Finds an entry of the container by its identifier and returns it.
     * @param string $id
     * @return mixed
     * @throws ContainerExceptionInterface
     */
    public function get(string $id): mixed
    {
        try {
            if (!$this->has($id)) {
                $this->checkInstantiability($id);
            }
            return $this->createInstance($id);
        } catch (ReflectionException|ServiceNotFoundException $e) {
            throw new ServiceNotFoundException($e->getMessage());
        } catch (Throwable $e) {
            throw new ContainerException($e->getMessage());
        }
    }

    /**
     * Checks if there is a binding in the container.
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     * @param string $id
     * @return bool
     */
    public function has(string $id): bool
    {
        return isset($this->bindings[$id]);
    }

    /**
     * Check possibility of creating an instance of $id
     * @param string $id
     * @return string
     * @throws ServiceNotFoundException
     * @throws ReflectionException
     */
    private function checkInstantiability(string $id): string
    {
        $reflection = $this->createReflection($id);

        if (!$reflection->isInstantiable()) {
            throw new ServiceNotFoundException("ServiceNotFoundException: {$id} is not instantiable");
        }

        return $id;
    }

    /**
     * Create a new reflection of $id
     * @param string $id
     * @return ReflectionClass
     * @throws ReflectionException
     */
    private function createReflection(string $id): ReflectionClass
    {
        $reflection = new ReflectionClass($id);

        if (!isset($reflection)) {
            throw new ReflectionException("ReflectionException: cannot create reflection of {$id}");
        }

        return $reflection;
    }

    /**
     * Processes the creation of instances by its id. Recursively create instances of its dependencies if needed.
     * Return instance of $id with all instances of parameters of its constructor
     * @param string $id
     * @return mixed
     * @throws ServiceNotFoundException | ReflectionException
     */
    private function createInstance(string $id): mixed
    {
        if ($this->has($id)) {
            $result = $this->resolveBinding($id);
            if ($result !== null) {
                return $result;
            }
        }

        $reflection = $this->createReflection($id);
        $constructor = $reflection->getConstructor();

        if (empty($constructor)) {
            return $this->handleNonParametricReflection($id, $reflection);
        }
        $parameters = $constructor->getParameters();

        if (empty($parameters)) return new $id;

        return $this->handleParametricReflection($id, $parameters);
    }

    /**
     * Resolves simple bindings for callables and singleton-objects. In other cases returns null.
     * @param string $id
     * @return mixed
     */
    private function resolveBinding(string $id): mixed
    {
        if (is_callable($this->bindings[$id])) {
            return call_user_func($this->bindings[$id]);
        }
        if (is_object($this->bindings[$id])) {
            return $this->bindings[$id];
        }
        return null;
    }

    /**
     * Processes cases for reflections without parameters. When reflection is instantiable - new instance of item is returned.
     * If reflection is not instantiable, but there is a binding for $id - resolver instance is created.
     * Exception is thrown in other cases
     * @param string $id
     * @param $reflection
     * @return mixed
     * @throws ServiceNotFoundException | ReflectionException
     */
    private function handleNonParametricReflection(string $id, $reflection): mixed
    {
        if ($reflection->isInstantiable()) {
            return new $id;
        }

        if (isset($this->bindings[$id])) {
            return $this->createInstance($this->bindings[$id]);
        }

        throw new ServiceNotFoundException("ServiceNotFoundException: {$id} is not instantiable");
    }

    /**
     * Processes cases for reflections with parameters.
     * Recursively creates instances for each parameter of the reflection and adds them to the result array.
     * Throws an exception, if any parameter is not instantiable.
     * @param $id
     * @param $parameters
     * @return mixed
     * @throws ServiceNotFoundException | ReflectionException
     */
    private function handleParametricReflection($id, $parameters): mixed
    {
        $parametersList = [];

        foreach ($parameters as $parameter) {
            if (!$parameter->getType() && !$parameter->isOptional()) {
                throw new ServiceNotFoundException("ServiceNotFoundException: Parameter {$parameter->getName()} of {$id} is not instantiable");
            }

            $parametersList[] = $this->createInstance($parameter->getType()->getName());
        }
        return new $id(...$parametersList);
    }

}