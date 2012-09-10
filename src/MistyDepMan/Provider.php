<?php

namespace MistyDepMan;

use MistyDepMan\Exception\DuplicateKeyException;
use MistyDepMan\Exception\UnknownKeyException;

class Provider
{
    private $entities;
    private $proxies;

    /**
     * Register an entity for this key - supports lazy loading
     * If $entity is a function, it will be invoked the first time $key is lookedup,
     * and the function will be replaced with its return value
     *
     * @param string $key The key that represents this object
     * @param mixed|function $entity The entity for $key, or a callable to lazy initialize it
     * @throws MistyDepMap\Exception\DuplicateKeyException If the key is already in use
     */
    public function register($key, $entity)
    {
        if (isset($this->entities[$key])) {
            throw new DuplicateKeyException("Duplicate key: $key");
        }

        $this->entities[$key] = $entity;
    }

    /**
     * Check whether ther is an entity registered for this key
     *
     * @param string $key The key that represents the object
     * @return bool
     */
    public function has($key)
    {
        return isset($this->entities[$key]);
    }

    /**
     * Retrieve the object for the given key, or throw an exception if it doesn't exist
     * If the registered entity is a function, it will execute it and
     *
     * @param string $key The key that represents the object
     * @return mixed The value registered for $key
     * @throws MistyDepMap\Exception\UnknownKeyException If not found
     */
    public function lookup($key)
    {
        if (!isset($this->entities[$key])) {
            throw new UnknownKeyException("Unknown key: $key");
        }

        if (is_callable($this->entities[$key])) {
            $this->entities[$key] = $this->entities[$key]();
        }

        return $this->entities[$key];
    }

    /**
     * Return a LazyLoadProxy for $class. The real class will be instanciated only when the one
     * of its method will be invoked
     * THIS METHOD WILL ALWAYS RETURN THE SAME PROXY FOR A GIVEN KEY
     *
     * @param string $className The full class name of the class we want to lazy load
     * @return LazyLoadProxy A lazy loader
     */
    public function proxy($className)
    {
        if (isset($this->proxies[$className])) {
            return $this->proxies[$className];
        }

        $this->proxies[$className] = new LazyLoadProxy(function() use ($className) {
            return $this->create($className);
        });

        return $this->proxies[$className];
    }

    /**
     * Create an instance of $class, inject itselt into it (if it's a IContainer) and then return it
     * THIS METHOD WILL CREATE A NEW INSTANCE EVERY TIME IT'S INVOKED
     *
     * @param string $className Must be a valid class name, and the class must be an instance of Container
     * @return object A new instance of class
     */
    public function create($className /* varargs...*/)
    {
        $args = array_slice(func_get_args(), 1);
        $reflectionObj = new \ReflectionClass($className);
        $instance = $reflectionObj->newInstanceArgs($args);

        if (method_exists($instance, 'setupContainer')) {
            $instance->setupContainer($this);
        }

        return $instance ;
    }
}
