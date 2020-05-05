<?php


namespace Lavra\Extendable\Extenders;


use Illuminate\Container\Container;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Arr;
use Lavra\Extendable\Contracts\Extenders\ExtenderContract;
use Lavra\Extendable\Extension;

class Middleware implements ExtenderContract
{
    /**
     * Middleware registered for the webapp.
     *
     * @var array
     */
    protected $web = [];

    /**
     * Middleware registered for the api.
     *
     * @var array
     */
    protected $api = [];

    /**
     * Middleware aliases to be registered.
     *
     * @var array
     */
    protected $aliases = [];

    /**
     * @inheritDoc
     * @throws BindingResolutionException
     */
    public function extend(Container $container, Extension $extension = null)
    {
        $router = $container->make('router');

        foreach ($this->web as $middleware) {
            $router->pushMiddlewareToGroup('web', $middleware);
        }

        foreach ($this->api as $middleware) {
            $router->pushMiddlewareToGroup('api', $middleware);
        }

        foreach ($this->aliases as $alias => $middleware) {
            $router->aliasMiddleware($alias, $middleware);
        }
    }

    /**
     * Pushes middleware to the specified array.
     *
     * @param $appendsTo
     * @param $middleware
     * @param array $moreMiddleware
     * @return Middleware
     */
    private function pushMiddleware($appendsTo, $middleware, ...$moreMiddleware)
    {
        if (is_array($middleware)) {
            $this->{$appendsTo} = array_merge($this->{$appendsTo}, $middleware);
        }

        if (is_string($middleware)) {
            array_push($this->{$appendsTo}, $middleware);
        }

        $moreMiddleware = Arr::flatten($moreMiddleware);
        if (count($moreMiddleware) > 0) {
            $this->{$appendsTo} = $this->{$appendsTo} + $moreMiddleware;
        }

        return $this;
    }

    /**
     * Adds a Middleware alias to be registered with the app.
     *
     * @param string|array $alias
     * @param string|null $middleware
     * @return Middleware
     */
    public function addAlias($alias, $middleware = null): Middleware
    {
        if (is_string($alias) && !is_null($middleware)) {
            $this->aliases[$alias] = $middleware;

            return $this;
        }

        if (is_array($alias)) {
            $this->aliases = array_merge($this->aliases, $alias);

            return $this;
        }

        return $this;
    }

    /**
     * Appends Middleware for the web group.
     *
     * @param $middleware
     * @param array $moreMiddleware
     * @return Middleware
     */
    public function forWeb($middleware, ...$moreMiddleware): Middleware
    {
        return $this->pushMiddleware('web', $middleware, $moreMiddleware);
    }

    /**
     * Appends Middleware for the api group.
     *
     * @param $middleware
     * @param array $moreMiddleware
     * @return Middleware
     */
    public function forApi($middleware, ...$moreMiddleware): Middleware
    {
        return $this->pushMiddleware('api', $middleware, $moreMiddleware);
    }
}