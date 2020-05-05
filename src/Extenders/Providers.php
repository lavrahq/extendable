<?php


namespace Lavra\Extendable\Extenders;


use Illuminate\Container\Container;
use Illuminate\Support\Facades\App;
use Lavra\Extendable\Contracts\Extenders\ExtenderContract;
use Lavra\Extendable\Extension;

class Providers implements ExtenderContract
{

    /**
     * The Service Providers to register.
     *
     * @var array|string
     */
    protected $providers;

    /**
     * Providers constructor.
     *
     * @param $providers
     */
    public function __construct($providers)
    {
        $this->providers = $providers;
    }

    /**
     * @inheritDoc
     */
    public function extend(Container $container, Extension $extension = null)
    {
        if (is_string($this->providers)) {
            $this->providers = [$this->providers];
        }

        foreach ($this->providers as $provider) {
            App::register($provider);
        }
    }

}