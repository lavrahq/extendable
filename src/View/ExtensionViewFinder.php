<?php

namespace Lavra\Extendable\View;

use Illuminate\View\FileViewFinder;
use Lavra\Extendable\Facades\Extend;

class ExtensionViewFinder extends FileViewFinder
{
    /**
     * Remove a location from the finder.
     *
     * @param  string  $location
     */
    public function removeLocation(string $location)
    {
        $key = array_search($location, $this->paths);

        if ($key) {
            unset($this->paths[$key]);
        }
    }

    /**
     * Get the path to a template with a named path.
     *
     * @param  string  $name
     * @return string
     */
    protected function findNamespacedView($name)
    {
        list($namespace, $view) = $this->parseNamespaceSegments($name);

        $this->addThemeNamespaceHints($namespace);

        return $this->findInPaths($view, $this->hints[$namespace]);
    }

    /**
     * Add namespace hints for the currently set theme.
     *
     * @param  string  $namespace
     * @return array
     */
    protected function addThemeNamespaceHints($namespace)
    {
        /** @var \Lavra\Extendable\Extension|null */
        $extension = Extend::activeTheme();

        if (is_null($extension)) {
            return;
        }

        $hints   = array_reverse($this->hints[$namespace]);
        $hints[] = $extension->themePath('views/' . $namespace);
        $hints[] = $extension->themePath('views/vendor/' . $namespace);
        $hints[] = $extension->themePath('views/extensions/' . $namespace);

        $this->hints[$namespace] = array_reverse($hints);
    }
}
