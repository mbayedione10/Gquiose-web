<?php

namespace App\Filament\Resources\UtilisateurResource\Pages;

use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\UtilisateurResource;
use Filament\Notifications\Notification;

class EditUtilisateur extends EditRecord
{
    protected static string $resource = UtilisateurResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Vérifier qu'au moins un contact (email ou phone) est renseigné
        if (empty($data['email']) && empty($data['phone'])) {
            Notification::make()
                ->title('Erreur de validation')
                ->body('Au moins un contact (email ou téléphone) doit être renseigné.')
                ->danger()
                ->send();

            $this->halt();
        }

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
