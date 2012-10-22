<?php

namespace MistyDepMan;

use MistyDepMan\Exception\DuplicateKeyException;
use MistyDepMan\Exception\UnknownKeyException;

class Provider
{
    /** @var array */
    private $entities;

    /** @var array */
    private $proxies;

    /**
     * @param array $entities
     * @param array $proxies
     */
    public function __construct(array $entities = array(), array $proxies = array())
    {
        $this->entities = $entities;
        $this->proxies = $proxies;
    }

    /**
     * Register an entity for this key - supports lazy loading
     * If $entity is a function, it will be invoked the first time $key is lookedup,
     * and the function will be replaced with its return value
     *
     * @param string $key The key that represents this object
     * @param mixed|callable $entity The entity for $key, or a callable to lazy initialize it
     * @throws DuplicateKeyException If the key is already in use
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
     * @throws UnknownKeyException If not found
     */
    public function lookup($key)
    {
        if (!isset($this->entities[$key])) {
            throw new UnknownKeyException("Unknown key: $key");
        }

        if (!$this->isInitialized($key)) {
            $callback = $this->entities[$key];
            $this->entities[$key] = $callback($this);
        }

        return $this->entities[$key];
    }

    public function isInitialized($key)
    {
        return !is_callable($this->entities[$key]);
    }

    /**
     * Return a LazyLoadProxy for $class. The real class will be instanciated only when the one
     * of its method will be invoked
     * THIS METHOD WILL ALWAYS RETURN THE SAME PROXY FOR A GIVEN CLASS NAME
     *
     * @param string $className The full class name of the class we want to lazy load
     * @return LazyLoadProxy A lazy loader
     */
    public function proxy($className /* varargs...*/)
    {
        if (!isset($this->proxies[$className])) {

            // creating a nre proxy
            $args = func_get_args();
            $this->proxies[$className] = new LazyLoadProxy(function() use ($args) {
                return call_user_func_array(
                    array($this, 'create'),
                    $args
                );
            });
        }

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
        if (is_array($className)) {
            $varargs = array_slice($className, 1);
            $className = $className[0];
        } else {
            $varargs = array_slice(func_get_args(), 1);
        }

        $reflectionObj = new \ReflectionClass($className);
        $instance = $reflectionObj->newInstanceArgs($varargs);

        if (method_exists($instance, 'setupContainer')) {
            $instance->setupContainer($this);
        }

        return $instance ;
    }
}
