<?php

namespace App\Filament\Resources;
use Filament\Resources\Resource;

use App\Models\Censure;
use App\Filament\Resources\CensureResource\Pages;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteBulkAction;

class CensureResource extends Resource
{
    protected static ?string $model = Censure::class;

    protected static ?string $navigationIcon = 'heroicon-o-no-symbol';
    protected static ?string $navigationGroup = 'Forum';
    protected static ?string $navigationLabel = 'Mots censurés';
    protected static ?int $navigationSort = 14;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make()->schema([
                TextInput::make('name')
                    ->label('Mot censuré')
                    ->required()
                    ->maxLength(255)
                    ->unique(Censure::class, 'name', ignorable: fn (?Censure $record) => $record)
                    ->placeholder('Ex: gros mot, insulte…'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Mot censuré')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Mot copié !'),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->emptyStateHeading('Aucun mot censuré')
            ->emptyStateDescription('Ajoute des mots à censurer pour nettoyer le forum.');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCensures::route('/'),
            'create' => Pages\CreateCensure::route('/create'),
            'edit'   => Pages\EditCensure::route('/{record}/edit'),
        ];
    }
}