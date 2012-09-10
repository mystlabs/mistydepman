<?php

use MistyDepMan\LazyLoadProxy;

class LazyLoadProxyTest extends MistyTesting\UnitTest
{
	public function testProzyCreation()
	{
		$proxy = new LazyLoadProxy(function(){
			return new LazyLoadProxyTest_Class();
		});
	}

	public function testInatilizationIsLazy()
	{
		$state = 0;
		$proxy = new LazyLoadProxy(function() use (&$state){
			$state = 1;
			return new LazyLoadProxyTest_Class();
		});

		$this->assertEquals(0, $state);

		$this->assertEquals('ok!', $proxy->doStuff());
		$this->assertEquals(1, $state);
	}

	/**
	 * @expectedException BadMethodCallException
	 */
	public function testCallBadMethod()
	{
		$proxy = new LazyLoadProxy(function(){
			return new LazyLoadProxyTest_Class();
		});
		$proxy->doWhat();
	}

	public function testCallMethodsWithParams()
	{
		$proxy = new LazyLoadProxy(function(){
			return new LazyLoadProxyTest_Class();
		});

		$this->assertEquals(6, $proxy->doOtherStuff(1, 2, 3));
	}
}

class LazyLoadProxyTest_Class
{
	public function doStuff()
	{
		return 'ok!';
	}

	public function doOtherStuff($a, $b, $c)
	{
		return $a + $b + $c;
	}
}
