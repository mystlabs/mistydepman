<?php

namespace MistyDepMan;

/**
 * Proxy to lazy load a class
 * The Closure to initialize the class will be invoked only when at the first method call
 */
class LazyLoadProxy
{
    private $initializer;
    private $instance;

    /**
     * @param \Closure $initializer The closure to create an instance of the wrapped class
     */
    public function __construct(\Closure $initializer)
    {
        $this->initializer = $initializer;
    }

    /**
     * Initialize the object on the first call, and forward all the subsequent calls to the instance
     */
    public function __call($method, $args)
    {
        if (!$this->instance)
        {
            $this->instance = call_user_func($this->initializer);
            if (!is_object($this->instance))
            {
                throw new \InvalidArgumentException(sprintf(
                    'The callable must return an object, returned a %s instead',
                    gettype($this->instance)
                ));
            }
        }

        if (!method_exists($this->instance, $method))
        {
            throw new \BadMethodCallException(sprintf(
                "Unknown method '%s' on '%s'",
                $method,
                get_class($this->instance)
            ));
        }

        return call_user_func_array(
            array($this->instance, $method),
            $args
        );
    }
}
