<?php

use MistyDepMan\Provider;
use MistyDepMan\Container;
use MistyDepMan\IContainer;

class ContainerTest extends MistyTesting\UnitTest
{
    public function testSetupContainer()
    {
        $impl = new ContainerImpl();
        $impl->setupContainer(new Provider());
    }

    public function testInitialize()
    {
        $impl = new ContainerImpl();
        $impl->setupContainer(new Provider());

        $this->assertEquals(1, $impl->state);
    }
}

class ContainerImpl
{
    use Container;

    public $state = 0;

    protected function initialize()
    {
        $this->state = 1;
    }
}
