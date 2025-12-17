<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationTemplateResource\Pages;
use App\Filament\Resources\NotificationTemplateResource\RelationManagers;
use App\Models\NotificationTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class NotificationTemplateResource extends Resource
{
    protected static ?string $model = NotificationTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-group';

    protected static ?string $navigationLabel = 'Templates de Notifications';

    protected static ?string $pluralLabel = 'Templates de Notifications';
    
    protected static ?string $navigationGroup = 'Notifications';

    protected static ?int $navigationSort = 9;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nom du template')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->maxLength(500)
                            ->rows(2),

                        Forms\Components\Select::make('category')
                            ->label('Catégorie')
                            ->options([
                                'cycle' => 'Cycle menstruel',
                                'content' => 'Nouveaux contenus',
                                'forum' => 'Forum',
                                'health_tips' => 'Conseils santé',
                                'admin' => 'Admin',
                                'other' => 'Autre',
                            ])
                            ->default('other')
                            ->required(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make()
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

                        Forms\Components\TextInput::make('action')
                            ->label('Action (route/URL)')
                            ->maxLength(255),

                        Forms\Components\FileUpload::make('image')
                            ->label('Image (optionnelle)')
                            ->image()
                            ->directory('notifications/images'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category')
                    ->label('Catégorie')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'cycle' => 'Cycle menstruel',
                        'content' => 'Nouveaux contenus',
                        'forum' => 'Forum',
                        'health_tips' => 'Conseils santé',
                        'admin' => 'Admin',
                        'other' => 'Autre',
                        default => $state,
                    })
                    ->color(fn ($state) => match($state) {
                        'cycle' => 'danger',
                        'content' => 'success',
                        'forum' => 'primary',
                        'health_tips' => 'warning',
                        'admin' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('title')
                    ->label('Titre')
                    ->searchable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('icon')
                    ->label('Icône'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'cycle' => 'Cycle menstruel',
                        'content' => 'Nouveaux contenus',
                        'forum' => 'Forum',
                        'health_tips' => 'Conseils santé',
                        'admin' => 'Admin',
                        'other' => 'Autre',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListNotificationTemplates::route('/'),
            'create' => Pages\CreateNotificationTemplate::route('/create'),
            'edit' => Pages\EditNotificationTemplate::route('/{record}/edit'),
        ];
    }    
}
