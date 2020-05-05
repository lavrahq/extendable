<?php


namespace Lavra\Extendable\Contracts\Extenders;


use Illuminate\Container\Container;
use Lavra\Extendable\Extension;

interface ExtenderContract
{

    /**
     * Extends the base application.
     *
     * @param Container $container
     * @param Extension|null $extension
     * @return mixed
     */
    public function extend(Container $container, Extension $extension = null);

}