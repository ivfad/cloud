<?php

namespace Core;
use Core\Exceptions\ContainerException;
use Core\Exceptions\ContainerNotFoundException;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionException;


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
     * Associate a singleton-pattern element to bindings
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
     * Associate a common(non-singleton) element to bindings
     * @param string $id
     * example#1.1: InterfaceName::class
     * example#1.2: ClassName::class
     * @param string|callable $resolver
     * example#2.1 (string): ClassName::class
     * ClassName::class should implement InterfaceName:class from example #2.1
     * example#2.2 (callable): function () {...}
     * @return $this
     */
    public function bind(string $id, string|callable $resolver): Container
    {
        $this->bindings[$id] = $resolver;
        return $this;
    }

    /**
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
     * Finds an entry of the container by its identifier and returns it.
     * @param string $id
     * @return mixed
     * @throws ContainerException
     * @throws ContainerNotFoundException
     */
    public function get(string $id): mixed
    {
        try {
            if (! $this->has($id)) {
                $this->checkInstantiability($id);
            }
            return $this->createInstance($id);
        } catch(ReflectionException|ContainerNotFoundException $e) {
            throw new ContainerNotFoundException($e->getMessage());
        } catch(ContainerException $e) {
            throw new ContainerException($e->getMessage());
        }

//        if (! $this->has($id)) {
//            try {
//                $this->checkInstantiability($id);
//            } catch (ReflectionException $e) {
//                echo $e->getMessage();
//            }
//        }
//
//        try {
//            $instance = $this->createInstance($id);
//            return $instance;
//        } catch(Exception $e) {
//            echo $e->getMessage();
//        }

        return null;
    }

    /**
     * Check possibility of creating an instance of $id
     * @param string $id
     * @return string
     * @throws ContainerException
     * @throws ReflectionException
     */
    protected function checkInstantiability(string $id): string
    {
        $reflection = $this->createReflection($id);

        if (! $reflection->isInstantiable()) {
            throw new ContainerException("{$id} is not instantiable");
        }

        return $id;
    }

    /**
     * Create a new reflection of $id
     * @param string $id
     * @return ReflectionClass
     * @throws ReflectionException
     */
    protected function createReflection(string $id): ReflectionClass
    {
        $reflection = new ReflectionClass($id);

        if (! isset($reflection)) {
            throw new ReflectionException("Cannot create reflection of {$id}");
        }

        return $reflection;
    }

    /**
     * Create instance of an item and recursively create instances of its dependencies(if needed)
     * Case 1 - an entry is associated with container and its resolver is callable - this resolver is called
     * Case 2 - an associated entry is singleton - its resolver (object) is returned
     * Case 3 - an instantiable item has empty constructor - means no dependencies to resolve, new instance of item is returned
     * Case 4 - $id is an abstract class or interface, resolver is set - resolver instance is created (recursion)
     * Case 5 - element without dependencies has no bindings - exception
     * Case 6 - no parameters needed - new instance of item is returned
     * Case 7 - for each parameter of the constructor recursively check and add parameter instance to list
     * Return instance of $id with all instances of parameters of its constructor
     * @param string $id
     * @return mixed
     * @throws ContainerException
     * @throws ContainerNotFoundException
     * @throws ReflectionException
     */
    protected function createInstance(string $id): mixed
    {
        //Case 1
        if ($this->has($id) && is_callable($this->bindings[$id])) {
            return call_user_func($this->bindings[$id]);
        }

        //Case 2
        if ($this->has($id) && is_object($this->bindings[$id])) {
            return $this->bindings[$id];
        }

        $reflection = $this->createReflection($id);
        $constructor = $reflection->getConstructor();

        //Case 3
        if (empty($constructor) && $reflection->isInstantiable()) return new $id;

        //Case 4
        if (empty($constructor) && isset($this->bindings[$id]))
        {
            $id = $this->bindings[$id];
            return $this->createInstance($id);
        }

        //Case 5
        if (empty($constructor) && !isset($this->bindings[$id]))
        {
            throw new ContainerNotFoundException("{$id} is not instantiable");
        }

        $parameters = $constructor->getParameters();
        $parametersList = [];

        //Case 6
        if (empty($parameters)) return new $id;

        //Case 7
        foreach ($parameters as $parameter) {
            if(!$parameter->getType() && !$parameter->isOptional()) {
                throw new ContainerException("Parameter {$parameter->getName()} of {$id} is not instantiable");
            }
            $parametersList []= $this->createInstance($parameter->getType()->getName());
        }

        return new $id(...$parametersList);
    }
}