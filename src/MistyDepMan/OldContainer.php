<?php

namespace MistyDepMan;

use Mist\Notifier;
use Mist\Component\Configuration;
use Mist\DependencyInjection\IProvider;

abstract class Container
{
	protected $provider; /* @var $provider Provider */
	protected $notifier; /* @var $notifier Mist\Notifier */
	protected $configuration; /* @var $notifier Mist\Component\Configuration */

	public function __construct( IProvider $provider )
	{
		$this->provider = $provider;
		$this->notifier = $this->provider->get( 'Mist\Notifier' );
		$this->configuration = $this->provider->getConfiguration();

		$this->init();
	}

	protected function init(){}

	public function fromConfiguration( $name, $default = null )
	{
		return $this->configuration->get( $name, $default );
	}
}
