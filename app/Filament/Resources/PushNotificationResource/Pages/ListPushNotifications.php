<?php

namespace App\Filament\Resources\PushNotificationResource\Pages;

use App\Filament\Resources\PushNotificationResource;
use App\Models\PushNotification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Rap2hpoutre\FastExcel\FastExcel;

class ListPushNotifications extends ListRecords
{
    protected static string $resource = PushNotificationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('export')
                ->label('Exporter en Excel')
                ->icon('heroicon-o-download')
                ->color('success')
                ->action(function () {
                    return $this->exportToExcel();
                }),
        ];
    }

    protected function exportToExcel()
    {
        $notifications = PushNotification::all()->map(function ($notification) {
            $deliveryRate = $notification->sent_count > 0
                ? round(($notification->delivered_count / $notification->sent_count) * 100, 2)
                : 0;

            $openRate = $notification->delivered_count > 0
                ? round(($notification->opened_count / $notification->delivered_count) * 100, 2)
                : 0;

            $clickRate = $notification->opened_count > 0
                ? round(($notification->clicked_count / $notification->opened_count) * 100, 2)
                : 0;

            return [
                'ID' => $notification->id,
                'Titre' => $notification->title,
                'Message' => $notification->message,
                'Type' => $notification->type,
                'Statut' => $notification->status,
                'Audience' => $notification->target_audience,
                'Envoyés' => $notification->sent_count,
                'Livrés' => $notification->delivered_count,
                'Ouverts' => $notification->opened_count,
                'Cliqués' => $notification->clicked_count,
                'Taux de livraison (%)' => $deliveryRate,
                'Taux d\'ouverture (%)' => $openRate,
                'Taux de conversion (%)' => $clickRate,
                'Programmé pour' => $notification->scheduled_at?->format('d/m/Y H:i'),
                'Envoyé le' => $notification->sent_at?->format('d/m/Y H:i'),
                'Créé le' => $notification->created_at->format('d/m/Y H:i'),
            ];
        });

        $filename = 'statistiques-notifications-' . now()->format('Y-m-d-His') . '.xlsx';

        return (new FastExcel($notifications))->download($filename);
    }
}
