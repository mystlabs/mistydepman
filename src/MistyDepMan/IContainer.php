<?php

namespace MistyDepMan;

interface IContainer
{
    /**
     * Add the provider to this container,
     * and call initialize to let the class complete the initialization
     * This method is called automatically if the class is created by the Provider
     *
     * @param Provider $provider The provider used to initialize this container
     */
    function setupContainer(Provider $provider);
}
