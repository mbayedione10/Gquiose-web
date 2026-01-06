<?php

namespace App\Models\Traits;

trait HasPermissions
{
    /**
     * Vérifie si l'utilisateur a une permission spécifique
     */
    public function hasPermission(string $permissionName): bool
    {
        // Les super admins ont toutes les permissions
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Vérifier si l'utilisateur a un rôle
        if (!$this->role) {
            return false;
        }

        // Vérifier si le rôle a la permission
        return $this->role->permissions()
            ->where('name', $permissionName)
            ->exists();
    }

    /**
     * Vérifie si l'utilisateur a au moins une des permissions
     */
    public function hasAnyPermission(array $permissions): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (!$this->role) {
            return false;
        }

        return $this->role->permissions()
            ->whereIn('name', $permissions)
            ->exists();
    }

    /**
     * Vérifie si l'utilisateur a toutes les permissions
     */
    public function hasAllPermissions(array $permissions): bool
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (!$this->role) {
            return false;
        }

        $count = $this->role->permissions()
            ->whereIn('name', $permissions)
            ->count();

        return $count === count($permissions);
    }

    /**
     * Obtenir toutes les permissions de l'utilisateur
     */
    public function getPermissions()
    {
        if ($this->isSuperAdmin()) {
            return \App\Models\Permission::all();
        }

        if (!$this->role) {
            return collect([]);
        }

        return $this->role->permissions;
    }

    /**
     * Obtenir les noms des permissions de l'utilisateur
     */
    public function getPermissionNames(): array
    {
        return $this->getPermissions()->pluck('name')->toArray();
    }
}
