<?php

namespace App\Models\Traits;

use Filament\Panel;

trait FilamentTrait
{
    /*
     * Returns whether the user is allowed to access Filament panel
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isSuperAdmin() || in_array($this->role?->name, [
            'Admin',
            'Super Admin',
            'Éditeur',
            'Modérateur'
        ]);
    }
}
