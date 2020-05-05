<?php


namespace Lavra\Extendable\Extenders;


use Illuminate\Container\Container;
use Lavra\Extendable\Contracts\Extenders\ExtenderContract;
use Lavra\Extendable\Extension;

class Translations implements ExtenderContract
{

    /**
     * The path where translations should be loaded from.
     *
     * @var array
     */
    protected $path;

    /**
     * Translations constructor.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @inheritDoc
     */
    public function extend(Container $container, Extension $extension = null)
    {
        app('translator')
            ->addNamespace($extension->getId(), $this->path);
    }

}