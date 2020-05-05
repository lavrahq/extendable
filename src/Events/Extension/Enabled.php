<?php


namespace Lavra\Extendable\Events\Extension;


use Lavra\Extendable\Extension;

class Enabled
{

    /**
     * The Extension that has been enabled.
     *
     * @var Extension
     */
    public $extension;

    /**
     * Enabled constructor.
     *
     * @param Extension $extension
     */
    public function __construct(Extension $extension)
    {
        $this->extension = $extension;
    }

}
