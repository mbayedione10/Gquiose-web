<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserNotificationPreferenceResource\Pages;
use App\Models\UserNotificationPreference;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class UserNotificationPreferenceResource extends Resource
{
    protected static ?string $model = UserNotificationPreference::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments';
    
    protected static ?string $navigationLabel = 'Préférences Notifications';
    
    protected static ?string $navigationGroup = 'Notifications';

    protected static ?int $navigationSort = 9;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Select::make('utilisateur_id')
                            ->label('Utilisateur')
                            ->relationship('utilisateur', 'email')
                            ->required()
                            ->searchable(),
                    ]),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Toggle::make('notifications_enabled')
                            ->label('Notifications activées')
                            ->default(true),
                        
                        Forms\Components\Toggle::make('cycle_notifications')
                            ->label('Notifications de cycle')
                            ->default(true),
                        
                        Forms\Components\Toggle::make('content_notifications')
                            ->label('Notifications de contenu')
                            ->default(true),
                        
                        Forms\Components\Toggle::make('forum_notifications')
                            ->label('Notifications du forum')
                            ->default(true),
                        
                        Forms\Components\Toggle::make('health_tips_notifications')
                            ->label('Conseils de santé')
                            ->default(true),
                        
                        Forms\Components\Toggle::make('admin_notifications')
                            ->label('Notifications admin')
                            ->default(true),
                    ])
                    ->columns(2),

                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Toggle::make('do_not_disturb')
                            ->label('Mode Ne pas déranger'),
                        
                        Forms\Components\TimePicker::make('quiet_start')
                            ->label('Début période silencieuse')
                            ->withoutSeconds(),
                        
                        Forms\Components\TimePicker::make('quiet_end')
                            ->label('Fin période silencieuse')
                            ->withoutSeconds(),
                    ])
                    ->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('utilisateur.email')
                    ->label('Utilisateur')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('notifications_enabled')
                    ->label('Activé')
                    ->boolean(),
                
                Tables\Columns\IconColumn::make('cycle_notifications')
                    ->label('Cycle')
                    ->boolean(),
                
                Tables\Columns\IconColumn::make('content_notifications')
                    ->label('Contenu')
                    ->boolean(),
                
                Tables\Columns\IconColumn::make('forum_notifications')
                    ->label('Forum')
                    ->boolean(),
                
                Tables\Columns\IconColumn::make('do_not_disturb')
                    ->label('Ne pas déranger')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('notifications_enabled')
                    ->label('Notifications activées'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserNotificationPreferences::route('/'),
            'create' => Pages\CreateUserNotificationPreference::route('/create'),
            'edit' => Pages\EditUserNotificationPreference::route('/{record}/edit'),
        ];
    }
}
