<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;
use Filament\Forms\Components\TextInput;
use App\Models\User;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('changePassword')
                ->label('Modifier le mot de passe')
                ->icon('heroicon-o-key')
                ->color('warning')
                ->form([
                    TextInput::make('new_password')
                        ->label('Nouveau mot de passe')
                        ->password()
                        ->required()
                        ->minLength(6)
                        ->maxLength(255),
                    TextInput::make('new_password_confirmation')
                        ->label('Confirmer le mot de passe')
                        ->password()
                        ->required()
                        ->same('new_password')
                        ->minLength(6)
                        ->maxLength(255),
                ])
                ->action(function (array $data): void {
                    $this->record->update([
                        'password' => \Hash::make($data['new_password'])
                    ]);
                    
                    \Filament\Notifications\Notification::make()
                        ->success()
                        ->title('Mot de passe modifié')
                        ->body('Le mot de passe a été mis à jour avec succès.')
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('Modifier le mot de passe')
                ->modalDescription('Veuillez saisir le nouveau mot de passe pour cet administrateur.')
                ->modalSubmitActionLabel('Modifier'),
            Actions\DeleteAction::make()
                ->label('Supprimer')
                ->requiresConfirmation()
                ->modalHeading('Supprimer l\'administrateur')
                ->modalDescription('Êtes-vous sûr de vouloir supprimer cet administrateur ? Cette action est irréversible.')
                ->modalSubmitActionLabel('Oui, supprimer')
                ->successNotificationTitle('Administrateur supprimé avec succès'),
        ];
    }
}
