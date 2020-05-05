<?php


namespace Lavra\Extendable\Events\Extension;


use Lavra\Extendable\Extension;

class Installed
{

    /**
     * The Extension that has been installed.
     *
     * @var Extension
     */
    public $extension;

    /**
     * Installed constructor.
     *
     * @param Extension $extension
     */
    public function __construct(Extension $extension)
    {
        $this->extension = $extension;
    }

}
