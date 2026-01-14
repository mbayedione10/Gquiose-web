<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlateformeResource\Pages;
use App\Models\Plateforme;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PlateformeResource extends Resource
{
    protected static ?string $model = Plateforme::class;

    protected static ?string $navigationLabel = 'Plateformes';

    protected static ?string $navigationGroup = 'VBG';

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?int $navigationSort = 13;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 1])->schema([
                    TextInput::make('nom')
                        ->label('Nom de la plateforme')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),

                    Textarea::make('description')
                        ->label('Description')
                        ->rows(3)
                        ->maxLength(65535),

                    TextInput::make('signalement_url')
                        ->label('URL de signalement')
                        ->url()
                        ->maxLength(500)
                        ->placeholder('https://...'),

                    Toggle::make('status')
                        ->label('Active')
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
                Tables\Columns\TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->toggleable(),

                Tables\Columns\IconColumn::make('status')
                    ->label('Active')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créée le')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('status')
                    ->label('Statut')
                    ->boolean()
                    ->trueLabel('Actives')
                    ->falseLabel('Inactives'),
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
            'index' => Pages\ListPlateformes::route('/'),
            'create' => Pages\CreatePlateforme::route('/create'),
            'edit' => Pages\EditPlateforme::route('/{record}/edit'),
        ];
    }
}
