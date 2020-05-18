<?php

namespace Lavra\Extendable\Concerns;

use Illuminate\Support\Collection;
use Lavra\Extendable\Extension;

trait ManagesThemes
{

    /**
     * Returns the Extension which has the active theme or null
     * if there is no theme installed.
     *
     * @return Extension|null
     */
    public function activeTheme()
    {
        return $this->enabled()
            ->filter(function (Extension $e) {
                return $e->providesTheme() &&
                    $e->isActiveTheme();
            })
            ->first();
    }

    /**
     * Returns a Collection of enabled Extensions that provide
     * a theme.
     *
     * @return Collection
     */
    public function providesTheme(): Collection
    {
        return $this->enabled()
            ->filter(function (Extension $e) {
                return $e->providesTheme();
            });
    }
}
