<?php


namespace Lavra\Extendable;


use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;
use Lavra\Extendable\Contracts\Database\Migrations\MigrationRepositoryContract;
use Lavra\Extendable\Contracts\ExtensionManagerContract;
use Lavra\Extendable\Contracts\Storage\ExtensionStorageContract;
use Lavra\Extendable\Database\Migrations\MigrationRepository;
use Lavra\Extendable\ExtensionManager;
use Lavra\Extendable\Facades\Extend;
use Lavra\Extendable\Storage\DatabaseExtensionStorage;
use Lavra\Extendable\View\ExtensionViewFinder;

class ExtendableServiceProvider extends ServiceProvider
{

    /**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

    /**
     * Boot package services.
     */
    public function boot()
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->registerConfig();
    }

    /**
     * Register package services.
     */
    public function register()
    {
        $this->registerExtensionManager();
        $this->registerExtensionStorage();
        $this->registerExtensionMigrationRepository();

        Extend::fetchExtensions();

        $this->app->booting(function (Container $container) {
            $container->make(ExtensionManagerContract::class)
                ->extend();
        });

        $this->registerViewServices();
    }

    /**
     * Registers the package ExtensionManager instances.
     */
    protected function registerExtensionManager()
    {
        $this->app->singleton(ExtensionManagerContract::class, function($app) {
            return $app->make(ExtensionManager::class);
        });

        $this->app->alias(ExtensionManagerContract::class, 'extension-manager');
    }

    /**
     * Registers the package ExtensionMigrationRespository instance.
     */
    protected function registerExtensionMigrationRepository()
    {
        $this->app->singleton(MigrationRepositoryContract::class, function($app) {
            return $app->make(MigrationRepository::class);
        });
    }

    /**
     * Registers the ExtensionStorageInterface implementation.
     */
    protected function registerExtensionStorage()
    {
        $this->app->singleton(ExtensionStorageContract::class, function($app) {
            return $app->make(DatabaseExtensionStorage::class);
        });
    }

    /**
     * Register and tag the package config for publishing.
     */
    protected function registerConfig()
    {
        $configPath = __DIR__ . '/../config/config.php';

        $this->mergeConfigFrom($configPath, 'extensions');
        $this->publishes([
            $configPath => config_path('extensions.php'),
        ], 'config');
    }

    protected function registerViewServices()
    {
        $this->app->singleton('view.finder', function($app) {
            return new ExtensionViewFinder($app['files'], $app['config']['view.paths'], null);
        });

        foreach (Extend::providesTheme() as $extension) {
            $parts = explode("/", $extension->getId());
            $namespace = end($parts);

            if (! $namespace) {
                continue;
            }

            app('view')
                ->addNamespace($namespace, $extension->themePath('views'));
        }
    }

}
