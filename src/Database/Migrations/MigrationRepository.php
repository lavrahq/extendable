<?php


namespace Lavra\Extendable\Database\Migrations;


use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Lavra\Extendable\Contracts\Database\Migrations\MigrationRepositoryContract;
use Lavra\Extendable\Extension;
use League\Flysystem\Exception;

class MigrationRepository implements MigrationRepositoryContract
{
    /**
     * The database connection.
     *
     * @var
     */
    protected $connection;

    /**
     * Returns the name of the extension migrations tbale.
     *
     * @var string
     */
    protected $table = "extension_migrations";

    /**
     * MigrationRepository constructor.
     * @param ConnectionInterface $connection
     */
    public function __construct(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function ran(Extension $extension): Collection
    {
        if (! $this->storeExists()) {
            throw new Exception('The `extension_migrations` table is missing.');
        }

        return $this->query()
            ->where('extension', $extension->getId())
            ->pluck('file');
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function saveRun($file, Extension $extension)
    {
        if (! $this->storeExists()) {
            throw new Exception('The `extension_migrations` table is missing.');
        }

        $this->query()
            ->insert([
                'extension' => $extension->getId(),
                'file' => basename($file)
            ]);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function removeRun($file, Extension $extension)
    {
        if (! $this->storeExists()) {
            throw new Exception('The `extension_migrations` table is missing.');
        }

        $this->query()
            ->where('extension', $extension->getId())
            ->where('file', basename($file))
            ->delete();
    }

    /**
     * @inheritDoc
     */
    public function storeExists(): bool
    {
        return $this->connection->table($this->table) !== null;
    }

    /**
     * Returns a Builder instance for the migrations table.
     *
     * @return Builder
     */
    public function query(): Builder
    {
        return $this->connection->table($this->table);
    }
}