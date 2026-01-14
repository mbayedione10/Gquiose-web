<?php

namespace App\Filament\Resources\AlerteResource\Pages;

use App\Filament\Resources\AlerteResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewAlerte extends ViewRecord
{
    protected static string $resource = AlerteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Modifier'),

            Actions\Action::make('confirmation')
                ->label('Confirmer')
                ->requiresConfirmation()
                ->icon('heroicon-o-check')
                ->color('success')
                ->visible(fn () => $this->record->etat === 'Non approuvée')
                ->modalHeading('Confirmation')
                ->modalDescription('Voulez-vous vraiment confirmer cette alerte ?')
                ->action(function () {
                    $this->record->etat = 'Confirmée';
                    $this->record->save();

                    Notification::make()
                        ->title('Information')
                        ->success()
                        ->body("L'alerte qui a pour référence **".$this->record->ref."** vient d'être confirmée")
                        ->send();
                }),

            Actions\Action::make('rejeter')
                ->label('Rejeter')
                ->requiresConfirmation()
                ->icon('heroicon-o-x-mark')
                ->color('danger')
                ->visible(fn () => $this->record->etat === 'Non approuvée')
                ->modalHeading('Confirmation')
                ->modalDescription('Voulez-vous vraiment rejeter cette alerte ?')
                ->action(function () {
                    $this->record->etat = 'Rejetée';
                    $this->record->save();

                    Notification::make()
                        ->title('Information')
                        ->success()
                        ->body("L'alerte qui a pour référence **".$this->record->ref."** vient d'être rejetée")
                        ->send();
                }),

            Actions\Action::make('description')
                ->label('Ajouter des détails')
                ->icon('heroicon-o-plus-circle')
                ->color('gray')
                ->modalHeading('Détails')
                ->modalDescription('Rajouter toutes les informations concernant cette alerte')
                ->form([
                    \Filament\Forms\Components\Textarea::make('description')
                        ->label('Information')
                        ->placeholder('Saisissez ici les informations')
                        ->default(fn () => $this->record->description)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->record->description = $data['description'];
                    $this->record->save();

                    Notification::make()
                        ->title('Information')
                        ->success()
                        ->body("Une nouvelle information vient d'être rajoutée")
                        ->send();
                }),
        ];
    }
}
