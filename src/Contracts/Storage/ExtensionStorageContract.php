<?php


namespace Lavra\Extendable\Contracts\Storage;


use Lavra\Extendable\Extension;

interface ExtensionStorageContract
{

    /**
     * Insert the Extension into storage.
     *
     * @param Extension $extension
     * @return mixed
     */
    public function insert(Extension $extension);

    /**
     * Delete the Extension from storage.
     *
     * @param Extension $extension
     * @return mixed
     */
    public function delete(Extension $extension);

    /**
     * Enable the Extension in storage.
     *
     * @param Extension $extension
     * @return mixed
     */
    public function enable(Extension $extension);

    /**
     * Returns the `id` for all Extensions that are enabled.
     *
     * @return array
     */
    public function enabledIds(): array;

    /**
     * Disable the Extension in storage.
     *
     * @param Extension $extension
     * @return mixed
     */
    public function disable(Extension $extension);

    /**
     * Returns the `id` for all Extensions that are disabled.
     *
     * @return array
     */
    public function disabledIds(): array;

    /**
     * Return an Extension's database data.
     *
     * @param Extension $extension
     * @param array $columns
     * @return mixed
     */
    public function find(Extension $extension, $columns = ['*']);

    /**
     * Returns whether the Extension is enabled or not.
     *
     * @param Extension $extension
     * @return bool
     */
    public function isEnabled(Extension $extension): bool;

}