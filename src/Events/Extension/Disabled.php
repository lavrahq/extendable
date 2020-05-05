<?php


namespace Lavra\Extendable\Events\Extension;


use Lavra\Extendable\Extension;

class Disabled
{

    /**
     * The Extension that has been disabled.
     *
     * @var Extension
     */
    public $extension;

    /**
     * Disabled constructor.
     *
     * @param Extension $extension
     */
    public function __construct(Extension $extension)
    {
        $this->extension = $extension;
    }

}
