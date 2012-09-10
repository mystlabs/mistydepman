<?php

namespace MistyDepMan;

trait Container
{
    protected $provider;

    /**
     * Implementation of setupContainer according to IContainer
     */
    public function setupContainer(Provider $provider)
    {
        $this->provider = $provider;

        $this->initialize();
    }

    /**
     * Called once the provider is setup, useful everything that need access to the Provider
     */
    protected function initialize()
    {
        // override in your class
    }
}
