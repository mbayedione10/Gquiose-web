<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ConseilResource\Pages;
use App\Models\Conseil;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ConseilResource extends Resource
{
    protected static ?string $model = Conseil::class;

    protected static ?string $navigationLabel = 'Conseils';

    protected static ?string $navigationGroup = 'Contenu Educatif';

    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\Select::make('categorie')
                            ->label('Catégorie')
                            ->options([
                                'SSR' => 'Santé Sexuelle et Reproductive',
                                'VBG' => 'Violences Basées sur le Genre',
                                'Autonomisation' => 'Autonomisation',
                                'Général' => 'Général',
                            ])
                            ->required()
                            ->default('Général'),

                        Forms\Components\Textarea::make('message')
                            ->label('Conseil')
                            ->placeholder('Entrez votre conseil ici...')
                            ->rows(4)
                            ->required()
                            ->maxLength(500),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('message')
                    ->label('Conseil'),
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
