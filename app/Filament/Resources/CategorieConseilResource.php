<?php

namespace App\Filament\Resources;

use App\Models\CategorieConseil;
use Filament\{Tables, Forms};
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\CategorieConseilResource\Pages;
use App\Filament\Resources\CategorieConseilResource\RelationManagers;

class CategorieConseilResource extends Resource
{
    protected static ?string $model = CategorieConseil::class;

    protected static ?string $navigationLabel = "Conseils VBG";
    protected static ?string $navigationGroup = "VBG";
    protected static ?string $navigationIcon = 'heroicon-o-light-bulb';
    protected static ?int $navigationSort = 13;

    protected static ?string $modelLabel = 'Conseil';
    protected static ?string $pluralModelLabel = 'Conseils VBG';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(2)->schema([
                    TextInput::make('nom')
                        ->label('Titre')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->columnSpan(2),

                    Select::make('type_alerte_id')
                        ->label('Type de violence')
                        ->relationship('typeAlerte', 'name')
                        ->placeholder('-- Choisir --')
                        ->searchable()
                        ->preload(),

                    Select::make('sous_type_violence_numerique_id')
                        ->label('Sous-type numérique')
                        ->relationship('sousTypeViolenceNumerique', 'nom')
                        ->placeholder('-- Choisir --')
                        ->searchable()
                        ->preload(),

                    Toggle::make('is_default')
                        ->label('Conseil par défaut')
                        ->helperText('Affiché si aucun conseil spécifique')
                        ->default(false),

                    Toggle::make('status')
                        ->label('Actif')
                        ->default(true),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom')
                    ->label('Titre')
                    ->searchable()
                    ->sortable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('type_label')
                    ->label('Type associé')
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('sections_count')
                    ->label('Sections')
                    ->counts('sections'),

                Tables\Columns\IconColumn::make('status')
                    ->label('Actif')
                    ->boolean(),
            ])
            ->defaultSort('nom')
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
            RelationManagers\SectionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategorieConseils::route('/'),
            'create' => Pages\CreateCategorieConseil::route('/create'),
            'edit' => Pages\EditCategorieConseil::route('/{record}/edit'),
        ];
    }
}
