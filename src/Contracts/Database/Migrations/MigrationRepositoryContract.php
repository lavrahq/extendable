<?php


namespace Lavra\Extendable\Contracts\Database\Migrations;


use Illuminate\Support\Collection;
use Lavra\Extendable\Extension;

interface MigrationRepositoryContract
{

    /**
     * The migrations that have already ran.
     *
     * @param Extension $extension
     * @return Collection
     */
    public function ran(Extension $extension): Collection;

    /**
     * Stores the migration run for an extension's migration file.
     *
     * @param $file
     * @param Extension $extension
     * @return mixed
     */
    public function saveRun($file, Extension $extension);

    /**
     * Removes a stored migration run for an extension's migration file.
     *
     * @param $file
     * @param Extension $extension
     * @return mixed
     */
    public function removeRun($file, Extension $extension);

    /**
     * Returns whether the store exists or not.
     *
     * @return bool
     */
    public function storeExists(): bool;

}