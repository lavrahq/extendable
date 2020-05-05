<?php


namespace Lavra\Extendable;

use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Lavra\Extendable\Contracts\Storage\ExtensionStorageContract;
use Lavra\Extendable\Database\Migrations\Migrator;
use Lavra\Extendable\Events\Extension\Disabled;
use Lavra\Extendable\Events\Extension\Disabling;
use Lavra\Extendable\Events\Extension\Enabled;
use Lavra\Extendable\Events\Extension\Enabling;
use Lavra\Extendable\Events\Extension\Uninstalled;
use Lavra\Extendable\Exceptions\ExtensionNotFoundException;
use League\Flysystem\Exception;

class ExtensionManager
{

    /**
     * The Application Container.
     *
     * @var Container
     */
    protected $container;

    /**
     * The Filesystem instance.
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Collection of Extensions registered found.
     *
     * @var Collection|null
     */
    protected $extensions;

    /**
     * The interface responsible for migrating.
     *
     * @var Migrator
     */
    protected $migrator;

    /**
     * ExtensionManager constructor.
     *
     * @param Container $container
     * @param Filesystem $filesystem
     * @param Migrator $migrator
     */
    public function __construct(
        Container $container,
        Filesystem $filesystem,
        Migrator $migrator
    )
    {
        $this->container = $container;
        $this->filesystem = $filesystem;
        $this->migrator = $migrator;
    }

    /**
     * @inheritDoc
     * @throws FileNotFoundException
     * @throws BindingResolutionException
     */
    public function fetchExtensions(): Collection
    {
        $extensions = new Collection();

        if (is_null($this->extensions) && $this->filesystem->exists($this->installedJsonPath())) {
            $installed = json_decode($this->filesystem->get($this->installedJsonPath()), true);

            foreach ($installed as $package) {
                if (Arr::get($package, 'type') != config('extensions.type') || is_null(Arr::get($package, 'name'))) {
                    continue;
                }

                $extension = $this->container->make(
                    Extension::class,
                    ['path' => $this->vendorPath(Arr::get($package, 'name')), 'composer' => $package]
                );

                $extension->setVersion(Arr::get($package, 'version'));
                $extensions->put($extension->getId(), $extension);
            }
        }

        $this->extensions = $extensions;

        return $extensions;
    }

    /**
     * Returns the path to the vendor directory.
     *
     * @param $append
     * @return string
     */
    public function vendorPath($append): string
    {
        return base_path('vendor' . DIRECTORY_SEPARATOR . $append);
    }

    /**
     * Returns the path to the Composer `installed.json` file.
     *
     * @return string
     */
    public function installedJsonPath(): string
    {
        return base_path('vendor/composer/installed.json');
    }

    /**
     * @inheritDoc
     */
    public function all(): Collection
    {
        return $this->extensions;

    }

    /**
     * @inheritDoc
     */
    public function enabled(): Collection
    {
        return $this->all()
            ->filter(function(Extension $e) {
                return $e->isEnabled();
            });
    }

    /**
     * @inheritDoc
     */
    public function disabled(): Collection
    {
        return $this->all()
            ->filter(function(Extension $e) {
                return !$e->isEnabled();
            });
    }

    /**
     * @inheritDoc
     * @throws ExtensionNotFoundException
     */
    public function find($id): Extension
    {
        if (! $this->extensions->has($id)) {
            throw new ExtensionNotFoundException($id);
        }

        return $this->extensions->get($id);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function enable(Extension $extension)
    {
        if ($extension->isEnabled()) {
            return;
        }

        event(new Enabling($extension));

        $this->migrate($extension, 'up');

        $extension->setEnabled();

        event(new Enabled($extension));

        foreach ($extension->lifecycleExtenders() as $extender) {
            $extender->onEnable($this->container, $extension);
        }
    }

    /**
     * @inheritDoc
     */
    public function disable(Extension $extension)
    {
        event(new Disabling($extension));

        $extension->setDisabled();

        event(new Disabled($extension));

        foreach ($extension->lifecycleExtenders() as $extender) {
            $extender->onDisable($this->container, $extension);
        }
    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function uninstall(Extension $extension)
    {
        if ($extension->isEnabled()) {
            throw new \Exception('Extension must be disabled before uninstalling.');
        }

        $this->storage()->delete($extension);

        $this->migrate($extension, 'down');

        event(new Uninstalled($extension));
    }

//    /**
//     * @inheritDoc
//     */
//    public function assetPath(Extension $extension, Asset $asset)
//    {
//        // TODO: Implement assetPath() method.
//    }

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function migrate(Extension $extension, $direction = 'up')
    {
        $extension->migrate($this->migrator, $direction);
    }

    /**
     * @inheritDoc
     */
    public function path(Extension $extension, $within = null): string
    {
        return $extension->path($within);
    }

    /**
     * Returns the bound ExtensionStorageInterface implementation.
     *
     * @return mixed
     * @throws BindingResolutionException
     */
    public function storage(): ExtensionStorageContract
    {
        return $this->container->make(ExtensionStorageContract::class);
    }

    /**
     * Call all Extenders for each enabled Extension.
     */
    public function extend()
    {
        $container = $this->container;

        $this->enabled()
            ->each(function (Extension $extension) use ($container) {
                $extension->extend($container);
            });
    }

}