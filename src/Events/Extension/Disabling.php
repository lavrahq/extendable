<?php


namespace Lavra\Extendable\Events\Extension;


use Lavra\Extendable\Extension;

class Disabling
{

    /**
     * The Extension that is being disabled.
     *
     * @var Extension
     */
    public $extension;

    /**
     * Disabling constructor.
     *
     * @param Extension $extension
     */
    public function __construct(Extension $extension)
    {
        $this->extension = $extension;
    }

}
