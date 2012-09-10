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

    public function testInterface()
    {
        $this->assertFalse(new ContainerImpl instanceof IContainer);
        $this->assertTrue(new ContainerImplWithInterface instanceof IContainer);
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

class ContainerImplWithInterface implements IContainer
{
    use Container;
}
