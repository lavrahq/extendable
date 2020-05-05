<?php


namespace Lavra\Extendable\Events\Extension;


use Lavra\Extendable\Extension;

class Uninstalled
{

    /**
     * The Extension that has been uninstalled.
     *
     * @var Extension
     */
    public $extension;

    /**
     * Uninstalled constructor.
     *
     * @param Extension $extension
     */
    public function __construct(Extension $extension)
    {
        $this->extension = $extension;
    }

}
