<?php

namespace App\Filament\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

trait HasResourcePermissions
{
    /**
     * Map des ressources aux permissions requises
     */
    protected static function getPermissionMap(): array
    {
        return [
            'ArticleResource' => 'manage_articles',
            'RubriqueResource' => 'manage_rubriques',
            'ThematiqueResource' => 'manage_thematiques',
            'FaqResource' => 'manage_faqs',
            'VideoResource' => 'manage_videos',
            'ConseilResource' => 'manage_conseils',
            'AlerteResource' => 'manage_alerts',
            'ResponseResource' => 'moderate_responses',
            'QuestionResource' => 'moderate_forum',
            'StructureResource' => 'manage_structures',
            'UserResource' => 'manage_users',
            'RoleResource' => 'manage_roles',
            'PermissionResource' => 'manage_roles',
            'UtilisateurResource' => 'view_utilisateurs',
            'NotificationTemplateResource' => 'manage_notifications',
            'PushNotificationResource' => 'manage_notifications',
        ];
    }

    /**
     * Obtenir la permission pour cette ressource
     */
    protected static function getRequiredPermission(): ?string
    {
        $className = class_basename(static::class);
        $map = static::getPermissionMap();
        
        return $map[$className] ?? null;
    }

    /**
     * Vérifie si l'utilisateur peut voir cette ressource
     */
    public static function canViewAny(): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        // Super admin peut tout voir
        if ($user->isSuperAdmin()) {
            return true;
        }

        $permission = static::getRequiredPermission();
        
        if (!$permission) {
            return true; // Pas de permission définie, accès par défaut
        }

        return $user->hasPermission($permission);
    }

    /**
     * Vérifie si l'utilisateur peut créer
     */
    public static function canCreate(): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        $permission = static::getRequiredPermission();
        
        if (!$permission) {
            return true;
        }

        return $user->hasPermission($permission);
    }

    /**
     * Vérifie si l'utilisateur peut modifier
     */
    public static function canEdit(Model $record): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        $permission = static::getRequiredPermission();
        
        if (!$permission) {
            return true;
        }

        return $user->hasPermission($permission);
    }

    /**
     * Vérifie si l'utilisateur peut supprimer
     */
    public static function canDelete(Model $record): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        if ($user->isSuperAdmin()) {
            return true;
        }

        $permission = static::getRequiredPermission();
        
        if (!$permission) {
            return true;
        }

        return $user->hasPermission($permission);
    }

    /**
     * Vérifie si l'utilisateur peut supprimer en masse
     */
    public static function canDeleteAny(): bool
    {
        return static::canCreate();
    }

    /**
     * Vérifie si l'utilisateur peut restaurer (soft delete)
     */
    public static function canRestore(Model $record): bool
    {
        return static::canEdit($record);
    }

    /**
     * Vérifie si l'utilisateur peut forcer la suppression
     */
    public static function canForceDelete(Model $record): bool
    {
        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        // Seuls les super admins peuvent forcer la suppression
        return $user->isSuperAdmin();
    }
}
