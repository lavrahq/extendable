<?php


namespace Lavra\Extendable\Contracts;


use Illuminate\Support\Collection;
use Lavra\Extendable\Extension;

interface ExtensionManagerContract
{

    /**
     * Fetches and returns an array of Extensions that
     * are installed.
     *
     * @return Collection
     */
    public function fetchExtensions(): Collection;

    /**
     * Returns a Collection of all Extensions.
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Returns a Collection of enabled Extensions.
     *
     * @return Collection
     */
    public function enabled(): Collection;

    /**
     * Returns a Collection of disabled Extensions.
     *
     * @return Collection
     */
    public function disabled(): Collection;

    /**
     * Finds and returns the Extension by id.
     *
     * @param $id
     * @return Extension
     */
    public function find($id): Extension;

    /**
     * Enabled the provided Extension.
     *
     * @param Extension $extension
     * @return mixed
     */
    public function enable(Extension $extension);

    /**
     * Disabled the provided Extension.
     *
     * @param Extension $extension
     * @return mixed
     */
    public function disable(Extension $extension);

    /**
     * Uninstalls the provided Extension.
     *
     * @param Extension $extension
     * @return mixed
     */
    public function uninstall(Extension $extension);

    /**
     * Returns the publish path for an Asset in an Extension.
     *
     * @param Extension $extension
     * @param Asset $asset
     * @return mixed
     */
//    public function assetPath(Extension $extension, Asset $asset);

    /**
     * Runs the database migrations for the Extension.
     *
     * @param Extension $extension
     * @param string $direction
     * @return mixed
     */
    public function migrate(Extension $extension, $direction = 'UP');

    /**
     * Returns the path to the Extension on the Filesystem.
     *
     * @param Extension $extension
     * @return string
     */
    public function path(Extension $extension): string;

}