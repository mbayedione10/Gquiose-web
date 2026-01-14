<?php

namespace App\Filament\Resources\AdminInvitationResource\Pages;

use App\Filament\Resources\AdminInvitationResource;
use App\Mail\AdminInvitationMail;
use App\Models\AdminInvitation;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CreateAdminInvitation extends CreateRecord
{
    protected static string $resource = AdminInvitationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['token'] = AdminInvitation::generateToken();
        $data['invited_by'] = Auth::id();
        $data['expires_at'] = now()->addHours(48);

        return $data;
    }

    protected function afterCreate(): void
    {
        Mail::to($this->record->email)->send(new AdminInvitationMail($this->record));

        Notification::make()
            ->title('Invitation envoyée')
            ->body('Un email d\'invitation a été envoyé à '.$this->record->email)
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Invitation créée avec succès';
    }
}
