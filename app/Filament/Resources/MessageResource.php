<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MessageResource\Pages;
use App\Filament\Resources\MessageResource\RelationManagers;
use App\Models\Message;
use App\Models\Theme;
use App\Models\Utilisateur;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MessageResource extends Resource
{
    protected static ?string $model = Message::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    protected static ?string $navigationLabel = "Sujets";
    protected static ?string $navigationGroup = "Forum";
    protected static ?int $navigationSort = 74;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\Select::make('theme_id')
                            ->label("Thème")
                            ->placeholder("Selectionnez un thème")
                            ->relationship('theme', 'name')
                            ->searchable()
                            ->options(
                                Theme::where('status', true)
                                    ->pluck('name', 'id')
                            )
                            ->columnSpan([
                                'default' => 12,
                                'md' => 12,
                                'lg' => 12,
                            ]),

                        Forms\Components\Select::make('utilisateur_id')
                            ->label("Utilisateur")
                            ->placeholder("Selectionnez un utilisateur")
                            ->relationship('utilisateur', 'email')
                            ->searchable()
                            ->required()
                            ->options(
                                Utilisateur::where('status', true)
                                    ->pluck('email', 'id')
                            )
                            ->columnSpan([
                                'default' => 12,
                                'md' => 12,
                                'lg' => 12,
                            ]),

                        Forms\Components\Textarea::make('question')
                            ->label("Question")
                            ->placeholder("Le message")
                            ->required()
                            ->columnSpan([
                                'default' => 12,
                                'md' => 12,
                                'lg' => 12,
                            ]),

                    ]),

                Forms\Components\Repeater::make('chats')
                    ->label("Chats")
                    ->relationship('chats')
                    ->columnSpan('full')
                    ->createItemButtonLabel("Ajout un message")
                    ->schema([

                        Forms\Components\Select::make('utilisateur_id')
                            ->label("Utilisateur")
                            ->placeholder("Selectionnez un utilisateur")
                            ->relationship('utilisateur', 'email')
                            ->searchable()
                            ->required()
                            ->options(
                                Utilisateur::where('status', true)
                                    ->pluck('email', 'id')
                            )
                            ->columnSpan([
                                'default' => 12,
                                'md' => 12,
                                'lg' => 12,
                            ]),

                        Forms\Components\Textarea::make('message')
                            ->label("Message")
                            ->placeholder("Le message")
                            ->required()
                            ->columnSpan([
                                'default' => 12,
                                'md' => 12,
                                'lg' => 12,
                            ]),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('theme.name')
                    ->label("Thème")
                    ->sortable(),

                Tables\Columns\TextColumn::make('utilisateur.name')
                    ->label("Utilisateur")
                    ->sortable(),

                Tables\Columns\TextColumn::make('question')
                    ->label("Question")
                    ->searchable(),

                Tables\Columns\TextColumn::make('chats_count')
                    ->label("Messages")
                    ->sortable()
                    ->counts('chats'),

                Tables\Columns\ToggleColumn::make('status')
                    ->label("Statut"),

                Tables\Columns\TextColumn::make('created_at')
                    ->label("Date de création")
                    ->date('d F Y H:i')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ChatsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMessages::route('/'),
            'create' => Pages\CreateMessage::route('/create'),
            'edit' => Pages\EditMessage::route('/{record}/edit'),
        ];
    }
}
