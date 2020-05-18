<?php

namespace Lavra\Extendable\Concerns;

use Illuminate\Container\Container;
use Lavra\Extendable\Extension;
use Lavra\Extendable\Facades\Extend;

trait ThemesApplication
{

    /**
     * Returns true if the Extension provides a theme for the application.
     *
     * @return boolean
     */
    public function providesTheme(): bool
    {
        $provides = $this->metaAttr('provides');

        if (! is_null($provides)) {
            if (is_array($provides)) {
                return in_array('theme', $provides);
            }

            return false;
        }

        return false;
    }

    /**
     * Returns the root theme path for the Extension or a path within the
     * root theme path. If no root theme path is set it defaults to a
     * theme directory within the extension's path.
     *
     * @param string|null $within
     * @return string
     */
    public function themePath($within = null)
    {
        $themePath =  $this->path($this->metaAttr('theme.root', 'theme'));

        if (is_null($within)) {
            return $themePath;
        }

        return $themePath . DIRECTORY_SEPARATOR . $within;
    }

    /**
     * Registers the Extension as the theme.
     *
     * @param Container $container
     * @return void
     */
    public function registerTheme(Container $container)
    {
        if ($parent = $this->metaAttr('theme.parent')) {
            $parent = Extend::enabled()
                ->filter(function (Extension $e) use ($parent) {
                    return $e->attr('name') === $parent;
                })
                ->first();

            if (! is_null($parent)) {
                $container->make('view.finder')
                    ->prependLocation($parent->themePath('views'));
            }
        }

        $container->make('view.finder')
            ->prependLocation($this->themePath('views'));
    }
}
