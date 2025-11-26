<?php

namespace App\Filament\Resources\UtilisateurResource\Pages;

use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\UtilisateurResource;

class EditUtilisateur extends EditRecord
{
    protected static string $resource = UtilisateurResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Ne jamais mettre à jour les champs platform et provider depuis le formulaire
        // car ils sont en lecture seule et gérés uniquement lors de l'inscription
        unset($data['platform']);
        unset($data['provider']);
        unset($data['provider_id']);
        unset($data['fcm_token']);
        unset($data['email_verified_at']);
        
        return $data;
    }
}
