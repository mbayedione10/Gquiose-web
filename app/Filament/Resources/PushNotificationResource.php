<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PushNotificationResource\Pages;
use App\Models\PushNotification;
use App\Models\NotificationTemplate;
use App\Models\Ville;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class PushNotificationResource extends Resource
{
    protected static ?string $model = PushNotification::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';
    
    protected static ?string $navigationLabel = 'Notifications Push';
    
    protected static ?string $pluralLabel = 'Notifications Push';

    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Select::make('template_id')
                            ->label('Utiliser un template')
                            ->placeholder('Choisir un template (optionnel)')
                            ->options(NotificationTemplate::all()->pluck('name', 'id'))
                            ->searchable()
                            ->reactive()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $template = NotificationTemplate::find($state);
                                    if ($template) {
                                        $set('title', $template->title);
                                        $set('message', $template->message);
                                        $set('icon', $template->icon);
                                        $set('action', $template->action);
                                        $set('image', $template->image);
                                    }
                                }
                            }),
                    ])
                    ->columns(1),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Titre')
                            ->required()
                            ->maxLength(65)
                            ->helperText('Maximum 65 caractères'),

                        Forms\Components\Textarea::make('message')
                            ->label('Message')
                            ->required()
                            ->maxLength(240)
                            ->helperText('Maximum 240 caractères')
                            ->rows(3),

                        Forms\Components\Select::make('icon')
                            ->label('Icône')
                            ->options([
                                '🔔' => '🔔 Cloche',
                                '📢' => '📢 Mégaphone',
                                '💊' => '💊 Pilule',
                                '🩺' => '🩺 Stéthoscope',
                                '❤️' => '❤️ Cœur',
                                '🩸' => '🩸 Cycle menstruel',
                                '🤰' => '🤰 Grossesse',
                                '👶' => '👶 Bébé',
                                '💡' => '💡 Conseil',
                                '📚' => '📚 Article',
                                '🎥' => '🎥 Vidéo',
                                '❓' => '❓ Quiz',
                                '🏥' => '🏥 Centre de santé',
                                '⚠️' => '⚠️ Alerte',
                                '💬' => '💬 Message',
                                '✅' => '✅ Validation',
                                'ℹ️' => 'ℹ️ Information',
                            ])
                            ->searchable()
                            ->placeholder('Choisir un emoji'),
                        
                        Forms\Components\FileUpload::make('image')
                            ->label('Image (optionnelle)')
                            ->image()
                            ->directory('notifications/images'),
                        
                        Forms\Components\TextInput::make('action')
                            ->label('Action (route/URL)')
                            ->maxLength(255),
                    ])
                    ->columns(2),
                
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->label('Type')
                            ->options([
                                'manual' => 'Manuel',
                                'automatic' => 'Automatique',
                                'scheduled' => 'Programmé',
                            ])
                            ->default('manual')
                            ->required()
                            ->reactive(),
                        
                        Forms\Components\DateTimePicker::make('scheduled_at')
                            ->label('Programmer pour')
                            ->visible(fn ($get) => $get('type') === 'scheduled')
                            ->required(fn ($get) => $get('type') === 'scheduled'),
                        
                        Forms\Components\Select::make('target_audience')
                            ->label('Audience cible')
                            ->options([
                                'all' => 'Tous les utilisateurs',
                                'filtered' => 'Utilisateurs filtrés',
                            ])
                            ->default('all')
                            ->required()
                            ->reactive(),
                    ])
                    ->columns(2),
                
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('filters.age_min')
                                    ->label('Âge minimum')
                                    ->numeric()
                                    ->minValue(10)
                                    ->maxValue(100),
                                
                                Forms\Components\TextInput::make('filters.age_max')
                                    ->label('Âge maximum')
                                    ->numeric()
                                    ->minValue(10)
                                    ->maxValue(100),
                                
                                Forms\Components\Select::make('filters.sexe')
                                    ->label('Sexe')
                                    ->options([
                                        'F' => 'Féminin',
                                        'M' => 'Masculin',
                                    ]),
                            ]),
                        
                        Forms\Components\Select::make('filters.ville_id')
                            ->label('Ville')
                            ->options(Ville::pluck('name', 'id'))
                            ->searchable(),
                    ])
                    ->visible(fn ($get) => $get('target_audience') === 'filtered')
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('message')
                    ->label('Message')
                    ->limit(50)
                    ->searchable(),
                
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->enum([
                        'manual' => 'Manuel',
                        'automatic' => 'Automatique',
                        'scheduled' => 'Programmé',
                    ])
                    ->colors([
                        'primary' => 'manual',
                        'warning' => 'automatic',
                        'success' => 'scheduled',
                    ]),
                
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Statut')
                    ->enum([
                        'pending' => 'En attente',
                        'sent' => 'Envoyé',
                        'failed' => 'Échoué',
                    ])
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'sent',
                        'danger' => 'failed',
                    ]),
                
                Tables\Columns\TextColumn::make('sent_count')
                    ->label('Envoyés')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('delivered_count')
                    ->label('Livrés')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('opened_count')
                    ->label('Ouverts')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('clicked_count')
                    ->label('Cliqués')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('scheduled_at')
                    ->label('Programmé pour')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('sent_at')
                    ->label('Envoyé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'manual' => 'Manuel',
                        'automatic' => 'Automatique',
                        'scheduled' => 'Programmé',
                    ]),
                
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'En attente',
                        'sent' => 'Envoyé',
                        'failed' => 'Échoué',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('send')
                    ->label('Envoyer')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (PushNotification $record) => $record->status === 'pending')
                    ->action(function (PushNotification $record) {
                        $service = app(\App\Services\PushNotificationService::class);
                        $service->sendNotification($record);
                    }),
                Tables\Actions\Action::make('duplicate')
                    ->label('Dupliquer')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('secondary')
                    ->action(function (PushNotification $record) {
                        $newNotification = $record->replicate();
                        $newNotification->status = 'pending';
                        $newNotification->sent_at = null;
                        $newNotification->sent_count = 0;
                        $newNotification->delivered_count = 0;
                        $newNotification->opened_count = 0;
                        $newNotification->clicked_count = 0;
                        $newNotification->scheduled_at = null;
                        $newNotification->save();

                        \Filament\Notifications\Notification::make()
                            ->title('Notification dupliquée avec succès')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPushNotifications::route('/'),
            'create' => Pages\CreatePushNotification::route('/create'),
            'view' => Pages\ViewPushNotification::route('/{record}'),
            'edit' => Pages\EditPushNotification::route('/{record}/edit'),
        ];
    }
}
