<?php

class ProviderTest extends MistyTesting\UnitTest
{
    private $provider;

    public function before()
    {
        $this->provider = new MistyDepMan\Provider;
    }

    public function testRegisterAndLookup()
    {
        $this->provider->register('key', 'value');
        $this->assertEquals('value', $this->provider->lookup('key'));
    }

    /**
     * @expectedException MistyDepMan\Exception\DuplicateKeyException
     */
    public function testRegisterDuplicate()
    {
        $this->provider->register('key', 'value');
        $this->provider->register('key', 'value');
    }

    /**
     * @expectedException MistyDepMan\Exception\UnknownKeyException
     */
    public function testLookupMissingEntity()
    {
        $this->provider->lookup('key');
    }

    public function testLookupLazyLoading()
    {
        $state = 0;
        $this->provider->register('key', function() use (&$state){
            $state = 1;
            return 'value';
        });

        $this->assertEquals(0, $state);

        $this->assertEquals('value', $this->provider->lookup('key'));
        $this->assertEquals(1, $state);
    }

    public function testProxyLazyLoading()
    {
        $proxy = $this->provider->proxy('ExampleClassNotUsingContainer');

        try
        {
            $proxy->doStuff();
            $this->fail('The constructor should have been called now!');
        }
        catch(InvalidArgumentException $e)
        {
            // expected at this point!
        }
    }

    public function testProviderAutoInjectItselfInProxies()
    {
        $proxy = $this->provider->proxy('ExampleClassUsingContainer');
        $this->assertNotNull($proxy->getProvider());
    }

    public function testProxyWithConstructorArgs()
    {
        $proxy = $this->provider->proxy('ExampleClassWithConstructorArguments', 'a', 2, array('c'));
        $proxy->doStuff();
    }
}

class ExampleClassNotUsingContainer
{
    public function __construct()
    {
        throw new InvalidArgumentException();
    }

    public function doStuff()
    {

    }
}

class ExampleClassUsingContainer
{
    use MistyDepMan\Container;

    public function getProvider()
    {
        // from Container
        return $this->provider;
    }
}

class ExampleClassWithConstructorArguments
{
    public function __construct($a, $b, $c)
    {
        if (!$a || !$b || !$c) {
            throw new InvalidArgumentException();
        }
    }

    public function doStuff()
    {

    }
}
