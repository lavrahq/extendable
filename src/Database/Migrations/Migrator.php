<?php


namespace Lavra\Extendable\Database\Migrations;

use Doctrine\DBAL\Driver\PDOException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Schema\Builder;
use Illuminate\Filesystem\Filesystem;
use Lavra\Extendable\Contracts\Database\Migrations\MigrationRepositoryContract;
use Lavra\Extendable\Extension;

class Migrator
{

    /**
     * The migration repository implementation.
     *
     * @var MigrationRepositoryContract
     */
    protected $repository;

    /**
     * The filesystem implementation.
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * The schema builder instance.
     *
     * @var Builder
     */
    protected $builder;

    /**
     * Migrator constructor
     * .
     * @param MigrationRepositoryContract $repository
     * @param ConnectionInterface $connection
     * @param Filesystem $filesystem
     */
    public function __construct(
        MigrationRepositoryContract $repository,
        ConnectionInterface $connection,
        Filesystem $filesystem
    )
    {
        $this->repository = $repository;
        $this->filesystem = $filesystem;

        $this->builder = $connection->getSchemaBuilder();

        try {
            // workaround for https://github.com/laravel/framework/issues/1186
            $connection->getDoctrineSchemaManager()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
        } catch (PDOException $e) {
            return;
        }
    }

    /**
     * Run the migrations for the Extension.
     *
     * @param Extension $extension
     * @throws \Exception
     */
    public function run(Extension $extension)
    {
        $files = $extension->migrationFiles()
            ->toArray();

        $migrated = $this->repository->ran($extension)
            ->toArray();

        $awaiting = array_diff($files, $migrated);

        $this->upMany($awaiting, $extension);
    }

    /**
     * Rolls back all applied migrations.
     *
     * @param Extension $extension
     * @throws \Exception
     */
    public function reset(Extension $extension)
    {
        $files = $extension->migrationFiles()
            ->toArray();

        $migrated = $this->repository->ran($extension)
            ->toArray();

        $this->downMany($migrated, $extension);
    }

    /**
     * Migrate the provided array of migrations up.
     *
     * @param array $migrations
     * @param Extension $extension
     * @throws \Exception
     */
    public function upMany(array $migrations, Extension $extension)
    {
        foreach ($migrations as $migration) {
            $this->runUp($migration, $extension);
        }
    }

    /**
     * Migrate the provided array of migrations down.
     *
     * @param array $migrations
     * @param Extension $extension
     * @throws \Exception
     */
    public function downMany(array $migrations, Extension $extension)
    {
        foreach ($migrations as $migration) {
            $this->runDown($migration, $extension);
        }
    }

    /**
     * Run the migration forward (up).
     *
     * TODO: Add logging
     *
     * @param $migration
     * @param Extension $extension
     * @throws \Exception
     */
    public function runUp($migration, Extension $extension)
    {
        $resolved = $this->resolve($migration, $extension);

        $this->withClosure($resolved);

        $this->repository->saveRun($migration, $extension);
    }

    /**
     * Revert the migration (down).
     *
     * TODO: Add logging
     *
     * @param $migration
     * @param Extension $extension
     * @throws \Exception
     */
    public function runDown($migration, Extension $extension)
    {
        $resolved = $this->resolve($migration, $extension);

        $this->withClosure($resolved, 'down');

        $this->repository->removeRun($migration, $extension);
    }

    /**
     * Runs the closure for the specified direction.
     *
     * @param $migration
     * @param string $direction
     * @throws \Exception
     */
    public function withClosure($migration, $direction = 'up')
    {
        if ($direction != "up" && $direction != "down") {
            throw new \Exception("Migration direction of `{$direction}` is not a valid direction.");
        }

        if (! is_array($migration)) {
            throw new \Exception('Migration file must return an array.');
        }

        if (array_key_exists($direction, $migration)) {
            call_user_func($migration[$direction], $this->builder);
        }
    }

    /**
     * Resolve the migration file to an array.
     * @param $migration
     * @param Extension $extension
     * @return mixed
     * @throws FileNotFoundException
     */
    public function resolve($migration, Extension $extension)
    {
        $file = $extension->migrationPath($migration, true);

        if ($this->filesystem->exists($file)) {
            return $this->filesystem->getRequire($file);
        }
    }

}
