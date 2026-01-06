<?php

namespace App\Filament\Resources;

use App\Models\SectionConseil;
use Filament\{Tables, Forms};
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\SectionConseilResource\Pages;
use App\Filament\Resources\SectionConseilResource\RelationManagers;

class SectionConseilResource extends Resource
{
    protected static ?string $model = SectionConseil::class;

    // Caché de la navigation - accessible via RelationManager
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'Section';
    protected static ?string $pluralModelLabel = 'Sections';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(2)->schema([
                    Select::make('categorie_conseil_id')
                        ->label('Conseil parent')
                        ->relationship('categorieConseil', 'nom')
                        ->required()
                        ->searchable()
                        ->preload()
                        ->columnSpan(2),

                    TextInput::make('titre')
                        ->label('Titre')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(2),

                    TextInput::make('emoji')
                        ->label('Icône')
                        ->maxLength(10)
                        ->placeholder('🆘'),

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
                Tables\Columns\TextColumn::make('emoji')
                    ->label('')
                    ->width('40px'),

                Tables\Columns\TextColumn::make('titre')
                    ->label('Titre')
                    ->searchable(),

                Tables\Columns\TextColumn::make('items_count')
                    ->label('Items')
                    ->counts('items'),

                Tables\Columns\IconColumn::make('status')
                    ->label('Actif')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSectionConseils::route('/'),
            'create' => Pages\CreateSectionConseil::route('/create'),
            'edit' => Pages\EditSectionConseil::route('/{record}/edit'),
        ];
    }
}
