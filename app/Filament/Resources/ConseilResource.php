<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConseilResource\Pages;
use App\Filament\Resources\ConseilResource\RelationManagers;
use App\Models\Conseil;
use App\Models\Rubrique;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ConseilResource extends Resource
{
    protected static ?string $model = Conseil::class;

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make("message")
                            ->label("Conseil")
                            ->placeholder("Conseil de cycle menstruel")
                            ->unique(
                                'rubriques',
                                'name',
                                fn(?Conseil $record) => $record
                            )
                            ->rules(['max:255', 'string'])
                            ->required()
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make("message")
                    ->label("Conseil")
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConseils::route('/'),
            'create' => Pages\CreateConseil::route('/create'),
            'edit' => Pages\EditConseil::route('/{record}/edit'),
        ];
    }
}
