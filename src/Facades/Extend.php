<?php


namespace Lavra\Extendable\Facades;


use Illuminate\Support\Facades\Facade;

/**
 * @method static fetchExtensions
 * @method static all
 * @method static enabled \Illuminate\Support\Collection
 * @method static disabled
 * @method static find
 * @method static enable
 * @method static disable
 * @method static uninstall
 * @method static migrate
 * @method static path
 * @method static storage
 * @method static extend
 */
class Extend extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'extension-manager';
    }

}
