<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CensureResource\Pages;
use App\Filament\Resources\CensureResource\RelationManagers;
use App\Models\Censure;
use App\Models\Conseil;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CensureResource extends Resource
{
    protected static ?string $model = Censure::class;

    protected static ?string $navigationIcon = 'heroicon-o-ban';

    protected static ?string $navigationGroup = "Forum";

    //protected static ?int $navigationSort = 14;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()
                    ->schema([
                        Forms\Components\TextInput::make("name")
                            ->label("Censure")
                            ->placeholder("Mot censuré")
                            ->unique(
                                'censures',
                                'name',
                                fn(?Censure $record) => $record
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
                Tables\Columns\TextColumn::make('name')
                    ->label("Mot censuré")
                    ->searchable()
                    ->sortable()
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
            'index' => Pages\ListCensures::route('/'),
            'create' => Pages\CreateCensure::route('/create'),
            'edit' => Pages\EditCensure::route('/{record}/edit'),
        ];
    }
}
