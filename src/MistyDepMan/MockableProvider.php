<?php

namespace MistyDepMan;

class MockableProvider extends Provider
{
    private $mockProxies = array();

    private $mockClasses = array();

    public function registerMockProxy($name, $object)
    {
        $this->mockProxies[$name] = $object;
    }

    public function proxy($name)
    {
        if (array_key_exists($name, $this->mockProxies)) {
            return $this->mockProxies[$name];
        }

        return parent::create(func_get_args());
    }

    public function registerMockClass($name, $object)
    {
        $this->mockClasses[$name] = $object;
    }

    public function create($name)
    {
        if (array_key_exists($name, $this->mockClasses)) {
            return $this->mockClasses[$name];
        }

        return parent::create(func_get_args());
    }
}
