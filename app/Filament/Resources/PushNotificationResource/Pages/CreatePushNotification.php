<?php

namespace App\Filament\Resources\PushNotificationResource\Pages;

use App\Filament\Resources\PushNotificationResource;
use App\Services\PushNotificationService;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreatePushNotification extends CreateRecord
{
    protected static string $resource = PushNotificationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set status to 'pending' by default if not set
        $data['status'] = $data['status'] ?? 'pending';

        return $data;
    }

    protected function afterCreate(): void
    {
        // Envoyer automatiquement la notification après la création
        try {
            $service = app(PushNotificationService::class);
            
            // Compter les utilisateurs ciblés
            $query = \App\Models\Utilisateur::query()
                ->whereNotNull('onesignal_player_id')
                ->where('status', true);
            
            $targetCount = $query->count();
            
            if ($targetCount === 0) {
                Notification::make()
                    ->title('Notification créée mais non envoyée')
                    ->body('Aucun utilisateur avec OneSignal player_id trouvé.')
                    ->warning()
                    ->send();
                return;
            }
            
            // Envoyer la notification
            $service->sendNotification($this->record);
            
            // Recharger pour obtenir les stats à jour
            $this->record->refresh();
            
            Notification::make()
                ->title('Notification créée et envoyée !')
                ->body("Envoyée avec succès à {$this->record->sent_count} utilisateur(s).")
                ->success()
                ->send();
                
        } catch (\Exception $e) {
            Notification::make()
                ->title('Notification créée mais erreur d\'envoi')
                ->body('Erreur: ' . $e->getMessage())
                ->danger()
                ->send();
                
            \Log::error('Error sending notification after creation', [
                'notification_id' => $this->record->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
