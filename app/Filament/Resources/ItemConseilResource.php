<?php

namespace App\Filament\Resources;

use App\Models\ItemConseil;
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
use Filament\Forms\Components\Textarea;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Resources\ItemConseilResource\Pages;

class ItemConseilResource extends Resource
{
    protected static ?string $model = ItemConseil::class;

    // Caché de la navigation - accessible via RelationManager
    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $modelLabel = 'Item';
    protected static ?string $pluralModelLabel = 'Items';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Informations')->schema([
                Grid::make(2)->schema([
                    Select::make('section_conseil_id')
                        ->label('Section parente')
                        ->relationship('sectionConseil', 'titre')
                        ->getOptionLabelFromRecordUsing(fn (SectionConseil $record) =>
                            "{$record->categorieConseil?->nom} > {$record->titre}"
                        )
                        ->required()
                        ->searchable()
                        ->preload()
                        ->columnSpan(2),

                    Textarea::make('contenu')
                        ->label('Contenu du conseil')
                        ->required()
                        ->rows(4)
                        ->placeholder('Si tu es en danger immédiat, appelle la police (117)')
                        ->helperText('Le texte qui sera affiché comme conseil')
                        ->columnSpan(2),

                    TextInput::make('ordre')
                        ->label('Ordre d\'affichage')
                        ->numeric()
                        ->default(0)
                        ->minValue(0),

                    Toggle::make('status')
                        ->label('Actif')
                        ->default(true)
                        ->inline(false),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sectionConseil.categorieConseil.nom')
                    ->label('Catégorie')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('sectionConseil.titre')
                    ->label('Section')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('contenu')
                    ->label('Contenu')
                    ->searchable()
                    ->limit(80)
                    ->wrap(),

                Tables\Columns\TextColumn::make('ordre')
                    ->label('Ordre')
                    ->sortable(),

                Tables\Columns\IconColumn::make('status')
                    ->label('Actif')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('section_conseil_id')
            ->filters([
                SelectFilter::make('section_conseil_id')
                    ->label('Section')
                    ->relationship('sectionConseil', 'titre')
                    ->searchable()
                    ->preload(),

                TernaryFilter::make('status')
                    ->label('Statut')
                    ->placeholder('Tous')
                    ->trueLabel('Actifs uniquement')
                    ->falseLabel('Inactifs uniquement'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListItemConseils::route('/'),
            'create' => Pages\CreateItemConseil::route('/create'),
            'edit' => Pages\EditItemConseil::route('/{record}/edit'),
        ];
    }
}
