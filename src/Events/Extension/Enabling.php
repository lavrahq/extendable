<?php


namespace Lavra\Extendable\Events\Extension;


use Lavra\Extendable\Extension;

class Enabling
{

    /**
     * The Extension that is being enabled.
     *
     * @var Extension
     */
    public $extension;

    /**
     * Enabling constructor.
     *
     * @param Extension $extension
     */
    public function __construct(Extension $extension)
    {
        $this->extension = $extension;
    }


}
