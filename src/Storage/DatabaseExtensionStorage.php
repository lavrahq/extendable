<?php


namespace Lavra\Extendable\Storage;


use Illuminate\Database\ConnectionInterface;
use Lavra\Extendable\Contracts\Storage\ExtensionStorageContract;
use Lavra\Extendable\Extension;

class DatabaseExtensionStorage implements ExtensionStorageContract
{

    /**
     * The database connection.
     *
     * @var ConnectionInterface
     */
    protected $database;

    public function __construct(ConnectionInterface $database)
    {
        $this->database = $database;
    }

    /**
     * @inheritDoc
     */
    public function insert(Extension $extension)
    {
        return $this->database
            ->table('extensions')
            ->insert([
                'id' => $extension->getId(),
                'is_enabled' => false,
            ]);
    }

    /**
     * @inheritDoc
     */
    public function find(Extension $extension, $columns = ['*'])
    {
        return $this->database
            ->table('extensions')
            ->find($extension->getId(), $columns);
    }

    /**
     * @inheritDoc
     */
    public function enabledIds(): array
    {
        $c = $this->database
            ->table('extensions')
            ->where('is_enabled', true)
            ->get('id');

        return $c->pluck('id')
            ->toArray();
    }

    /**
     * @inheritDoc
     */
    public function disabledIds(): array
    {
        $c = $this->database
            ->table('extensions')
            ->where('is_enabled', false)
            ->get('id');

        return $c->pluck('id')
            ->toArray();
    }

    /**
     * @inheritDoc
     */
    public function delete(Extension $extension)
    {
        return $this->database
            ->table('extensions')
            ->delete($extension->getId());
    }

    /**
     * @inheritDoc
     */
    public function enable(Extension $extension)
    {
        return $this->setEnabled($extension, true);
    }

    /**
     * @inheritDoc
     */
    public function disable(Extension $extension)
    {
        return $this->setEnabled($extension, false);
    }

    /**
     * Set the Extension enabled or disabled by boolean.
     *
     * @param Extension $extension
     * @param bool $enabled
     * @return int
     */
    private function setEnabled(Extension $extension, $enabled = true)
    {
        return $this->database
            ->table('extensions')
            ->where('id', $extension->getId())
            ->update([
                'is_enabled' => $enabled
            ]);
    }

    /**
     * Returns true if the Extension is enabled.
     *
     * @param Extension $extension
     * @return boolean
     */
    public function isEnabled(Extension $extension): bool
    {
        $e = $this->find($extension);

        if (is_null($e)) {
            return false;
        }

        return $e->is_enabled;
    }

    /**
     * Returns true if the Extension is enabled and also the
     * active theme.
     *
     * @param Extension $extension
     * @return boolean
     */
    public function isActiveTheme(Extension $extension): bool
    {
        $e = $this->find($extension);

        if (is_null($e)) {
            return false;
        }

        return $e->is_enabled &&
            $e->is_active_theme;
    }

}
