<?php


namespace Lavra\Extendable;


use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Lavra\Extendable\Contracts\Extenders\ExtenderLifecycleContract;
use Lavra\Extendable\Database\Migrations\Migrator;
use Lavra\Extendable\Contracts\Storage\ExtensionStorageContract;
use League\Flysystem\Exception;
use League\Flysystem\FilesystemInterface;

class Extension
{

    /**
     * Unique identifier for the Extension.
     *
     * @var string
     */
    protected $id;

    /**
     * The directory where the Extension is located on the filesystem.
     *
     * @var string
     */
    protected $path;

    /**
     * The `composer.json` file contents in flat array format.
     *
     * @var array
     */
    protected $composer = [];

    /**
     * The installed version of the Extension.
     *
     * @var string
     */
    protected $version;

    /**
     * The Filesystem interface instance.
     *
     * @var Filesytem
     */
    protected $filesystem;

    /**
     * The Storage interface instance.
     *
     * @var ExtensionStorageContract
     */
    protected $storage;

    /**
     * Extension constructor.
     *
     * @param $path string
     * @param $composer
     * @param Filesystem $filesystem
     * @param ExtensionStorageContract $storage
     */
    public function __construct($path, $composer, Filesystem $filesystem, ExtensionStorageContract $storage)
    {
        $this->path = $path;
        $this->composer = $composer;
        $this->filesystem = $filesystem;
        $this->storage = $storage;

        $this->assignId();

        if (is_null($this->storage->find($this))) {
            $this->storage->insert($this);
        }
    }

    /**
     * Set the `id` property based on the Composer package name.
     */
    public function assignId()
    {
        [$vendor, $package] = explode('/', $this->attr('name'));
        $package = str_replace(config('extensions.convention'), '', $package);

        $this->id = implode('/', [$vendor, $package]);
    }

    /**
     * Returns the `id` property.
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the Composer array.
     *
     * @return array
     */
    public function getComposer(): array
    {
        return $this->composer;
    }

    /**
     * Sets the version of the Extension.
     *
     * @param string $version
     */
    public function setVersion(string $version)
    {
        $this->version = $version;
    }

    /**
     * Returns the attribute on the `composer.json` file.
     *
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public function attr($key, $default = null)
    {
        if (Arr::has($this->composer, $key)) {
            return Arr::get($this->composer, $key);
        }

        return $default;
    }

    /**
     * Returns the metadata attribute by key if it exists, otherwise
     * returns the default value.
     *
     * @param $key
     * @param null|mixed $default
     * @return mixed|null
     */
    public function metaAttr($key, $default = null)
    {
        $key = implode('.', ["extra", config('extensions.extra'), $key]);

        if (Arr::has($this->composer, $key)) {
            return Arr::get($this->composer, $key);
        }

        return $default;
    }

    /**
     * Return the path within the Extension directory.
     *
     * @param $within
     * @return string
     */
    public function path($within = null): string
    {
        $base = base_path('vendor/' . $this->attr('name'));

        if (is_null($within)) {
            return $base;
        }

        return $base . DIRECTORY_SEPARATOR . $within;
    }

    /**
     * Returns the full path to the `extension.php` file within the
     * Extension's directory.
     *
     * @return string|null
     */
    public function extensionFile()
    {
        if (! $this->filesystem->exists($this->path('extension.php'))) {
            return null;
        }

        return $this->path('extension.php');
    }

    /**
     * Returns true or false depending on whether the extension includes an
     * `extension.php` file.
     *
     * @return bool
     */
    public function hasExtensionFile(): bool
    {
        return $this->extensionFile() !== null;
    }

    /**
     * Returns true or false depending on whether the Extension
     * is enabled or not.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->storage
            ->isEnabled($this);
    }

    /**
     * Set the Extension enabled.
     */
    public function setEnabled()
    {
        $this->storage
            ->enable($this);
    }

    /**
     * Set the Extension disabled.
     */
    public function setDisabled()
    {
        $this->storage
            ->disable($this);
    }

    /**
     * Returns all registered extenders for this Extension.
     *
     * @return array
     */
    public function extenders(): array
    {
        if (! $this->hasExtensionFile()) {
            return [];
        }

        $extend = require $this->extensionFile();

        if (is_object($extend)) {
            return [$extend];
        }

        if (is_array($extend)) {
            return $extend;
        }

        return [];
    }

    /**
     *
     *
     * @return array
     */
    public function lifecycleExtenders(): array
    {
        return array_filter(
            $this->extenders(),
            function ($extender) {
                return $extender instanceof ExtenderLifecycleContract;
            }
        );
    }


    /**
     * Extends the base app with Extenders registered for this extension.
     *
     * @param Container $container
     */
    public function extend(Container $container)
    {
        foreach ($this->extenders() as $extender) {
            $extender->extend($container, $this);
        }
    }

    /**
     * Migrate the Extension the specified direction.
     *
     * @param Migrator $migrator
     * @param string $direction
     * @throws \Exception
     */
    public function migrate(Migrator $migrator, $direction = 'up')
    {
        if (! $this->hasMigrations()) {
            return;
        }

        if ($direction == 'up') {
            $migrator->run($this);
        } else {
            $migrator->reset($this);
        }
    }

    /**
     * Returns the path containing migration files.
     *
     * @param null $within
     * @param bool $addExtension
     * @return string
     */
    public function migrationPath($within = null, $addExtension = false): string
    {
        $append = $within ? $within . ($addExtension ? '.php' : '') : $within;

        return $this->path(
            $this->metaAttr('migrations', 'migrations') . DIRECTORY_SEPARATOR . $append
        );
    }

    /**
     * Returns the migration files for this extension.
     *
     * @return Collection
     */
    public function migrationFiles(): Collection
    {
        $files = $this->filesystem->glob($this->migrationPath() . '/*_*.php');

        if ($files === false) {
            return collect([]);
        }

        $files = collect($files);
        $files = $files->map(function ($file) {
            return str_replace('.php', '', $this->filesystem->basename($file));
        });

        $files->sort();

        return $files;
    }

    /**
     * Returns whether this Extension has migrations or not.
     *
     * @return bool
     */
    public function hasMigrations(): bool
    {
        return $this->migrationFiles()->count() > 0;
    }

}
