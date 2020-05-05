<?php


namespace Lavra\Extendable\Contracts\Extenders;


use Illuminate\Container\Container;
use Lavra\Extendable\Extension;

interface ExtenderLifecycleContract
{

    /**
     * Executed when an Extension is enabled with the specific Extender
     * registered.
     *
     * @param Container $container
     * @param Extension $extension
     * @return mixed
     */
    public function onEnable(Container $container, Extension $extension);

    /**
     * Executed when an Extension is disabled with the specific Extender
     * registered.
     *
     * @param Container $container
     * @param Extension $extension
     * @return mixed
     */
    public function onDisable(Container $container, Extension $extension);

}