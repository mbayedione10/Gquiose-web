<?php

namespace App\Filament\Resources\SectionConseilResource\RelationManagers;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $recordTitleAttribute = 'contenu';

    protected static ?string $title = 'Conseils';

    public function form(Form $form): Form
    {
        return $form->schema([
            Textarea::make('contenu')
                ->label('Conseil')
                ->required()
                ->rows(3)
                ->placeholder('Texte du conseil...'),

            Toggle::make('status')
                ->label('Actif')
                ->default(true),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('contenu')
                    ->label('Conseil')
                    ->searchable()
                    ->limit(80)
                    ->wrap(),

                Tables\Columns\IconColumn::make('status')
                    ->label('Actif')
                    ->boolean(),
            ])
            ->defaultSort('ordre')
            ->reorderable('ordre')
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Ajouter'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
