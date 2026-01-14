<?php

namespace App\Filament\Resources\CategorieConseilResource\RelationManagers;

use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class SectionsRelationManager extends RelationManager
{
    protected static string $relationship = 'sections';

    protected static ?string $recordTitleAttribute = 'titre';

    protected static ?string $title = 'Sections et conseils';

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('titre')
                ->label('Titre de la section')
                ->required()
                ->maxLength(255)
                ->placeholder('Ex: SÉCURITÉ IMMÉDIATE'),

            TextInput::make('emoji')
                ->label('Icône')
                ->maxLength(10)
                ->placeholder('🆘'),

            Toggle::make('status')
                ->label('Actif')
                ->default(true),

            Repeater::make('items')
                ->label('Conseils')
                ->relationship()
                ->schema([
                    Textarea::make('contenu')
                        ->label('Conseil')
                        ->required()
                        ->rows(2)
                        ->placeholder('Texte du conseil...'),

                    Toggle::make('status')
                        ->label('Actif')
                        ->default(true)
                        ->inline(),
                ])
                ->defaultItems(0)
                ->addActionLabel('Ajouter un conseil')
                ->reorderable()
                ->collapsible()
                ->itemLabel(fn (array $state): ?string => $state['contenu'] ?? null),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('emoji')
                    ->label('')
                    ->width('30px'),

                Tables\Columns\TextColumn::make('titre')
                    ->label('Section')
                    ->searchable(),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('Conseils')
                    ->counts('items'),

                Tables\Columns\IconColumn::make('status')
                    ->label('Actif')
                    ->boolean(),
            ])
            ->defaultSort('ordre')
            ->reorderable('ordre')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Ajouter une section'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
