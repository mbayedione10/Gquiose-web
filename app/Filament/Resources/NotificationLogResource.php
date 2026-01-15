<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationLogResource\Pages;
use App\Models\NotificationLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NotificationLogResource extends Resource
{
    protected static ?string $model = NotificationLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationLabel = 'Historique Notifications';

    protected static ?string $modelLabel = 'Historique Notification';

    protected static ?string $pluralModelLabel = 'Historique Notifications';

    protected static ?string $navigationGroup = 'Notifications';

    protected static ?int $navigationSort = 2;

    // Masquer de la navigation principale (accessible via relation dans PushNotifications)
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('utilisateur_id')
                            ->label('Utilisateur')
                            ->relationship('utilisateur', 'nom')
                            ->searchable()
                            ->disabled(),

                        Forms\Components\TextInput::make('title')
                            ->label('Titre')
                            ->disabled(),

                        Forms\Components\Textarea::make('message')
                            ->label('Message')
                            ->disabled(),

                        Forms\Components\TextInput::make('icon')
                            ->label('Icône')
                            ->disabled(),

                        Forms\Components\Select::make('type')
                            ->label('Type')
                            ->options([
                                'manual' => 'Manuel',
                                'automatic' => 'Automatique',
                                'scheduled' => 'Programmé',
                            ])
                            ->disabled(),

                        Forms\Components\Select::make('category')
                            ->label('Catégorie')
                            ->options([
                                'alert' => 'Alerte',
                                'reminder' => 'Rappel',
                                'health_tip' => 'Conseil santé',
                                'cycle' => 'Cycle',
                                'general' => 'Général',
                                'quiz' => 'Quiz',
                                'article' => 'Article',
                                'video' => 'Vidéo',
                            ])
                            ->disabled(),

                        Forms\Components\Select::make('status')
                            ->label('Statut')
                            ->options([
                                'pending' => 'En attente',
                                'sent' => 'Envoyée',
                                'delivered' => 'Livrée',
                                'opened' => 'Ouverte',
                                'clicked' => 'Cliquée',
                                'failed' => 'Échouée',
                            ])
                            ->disabled(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Détails de livraison')
                    ->schema([
                        Forms\Components\DateTimePicker::make('sent_at')
                            ->label('Envoyée le')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('delivered_at')
                            ->label('Livrée le')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('opened_at')
                            ->label('Ouverte le')
                            ->disabled(),

                        Forms\Components\DateTimePicker::make('clicked_at')
                            ->label('Cliquée le')
                            ->disabled(),

                        Forms\Components\TextInput::make('platform')
                            ->label('Plateforme')
                            ->disabled(),

                        Forms\Components\TextInput::make('fcm_message_id')
                            ->label('FCM Message ID')
                            ->disabled(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('utilisateur.nom')
                    ->label('Utilisateur')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('message')
                    ->label('Message')
                    ->limit(40)
                    ->wrap(),

                Tables\Columns\TextColumn::make('icon')
                    ->label('Icône'),

                Tables\Columns\TextColumn::make('category')
                    ->label('Catégorie')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'alert' => 'danger',
                        'reminder' => 'warning',
                        'health_tip' => 'success',
                        'cycle' => 'primary',
                        'general' => 'gray',
                        'quiz', 'article', 'video' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'alert' => 'Alerte',
                        'reminder' => 'Rappel',
                        'health_tip' => 'Conseil',
                        'cycle' => 'Cycle',
                        'general' => 'Général',
                        'quiz' => 'Quiz',
                        'article' => 'Article',
                        'video' => 'Vidéo',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending' => 'gray',
                        'sent' => 'primary',
                        'delivered' => 'success',
                        'opened' => 'info',
                        'clicked' => 'warning',
                        'failed' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending' => 'En attente',
                        'sent' => 'Envoyée',
                        'delivered' => 'Livrée',
                        'opened' => 'Ouverte',
                        'clicked' => 'Cliquée',
                        'failed' => 'Échouée',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('platform')
                    ->label('Plateforme')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'android' => 'success',
                        'ios' => 'primary',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('sent_at')
                    ->label('Envoyée le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('sent_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'pending' => 'En attente',
                        'sent' => 'Envoyée',
                        'delivered' => 'Livrée',
                        'opened' => 'Ouverte',
                        'clicked' => 'Cliquée',
                        'failed' => 'Échouée',
                    ]),

                Tables\Filters\SelectFilter::make('category')
                    ->label('Catégorie')
                    ->options([
                        'alert' => 'Alerte',
                        'reminder' => 'Rappel',
                        'health_tip' => 'Conseil santé',
                        'cycle' => 'Cycle',
                        'general' => 'Général',
                        'quiz' => 'Quiz',
                        'article' => 'Article',
                        'video' => 'Vidéo',
                    ]),

                Tables\Filters\SelectFilter::make('platform')
                    ->label('Plateforme')
                    ->options([
                        'android' => 'Android',
                        'ios' => 'iOS',
                    ]),

                Tables\Filters\Filter::make('sent_at')
                    ->form([
                        Forms\Components\DatePicker::make('sent_from')
                            ->label('Envoyé du'),
                        Forms\Components\DatePicker::make('sent_until')
                            ->label('Envoyé au'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['sent_from'], fn ($query, $date) => $query->whereDate('sent_at', '>=', $date))
                            ->when($data['sent_until'], fn ($query, $date) => $query->whereDate('sent_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotificationLogs::route('/'),
            'view' => Pages\ViewNotificationLog::route('/{record}'),
        ];
    }
}
