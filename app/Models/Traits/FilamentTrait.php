<?php

namespace App\Models\Traits;

trait FilamentTrait
{
    /*
     * Returns whether the user is allowed to access Filament panel
     */
    public function canAccessPanel(\Filament\Panel $panel): bool
    {
        return $this->isSuperAdmin();
    }
}
