<?php

namespace App\Filament\Resources;

use App\Models\SousTypeViolenceNumerique;
use Filament\{Tables, Forms};
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Filters\TernaryFilter;
use App\Filament\Resources\SousTypeViolenceNumeriqueResource\Pages;

class SousTypeViolenceNumeriqueResource extends Resource
{
    protected static ?string $model = SousTypeViolenceNumerique::class;

    protected static ?string $navigationLabel = "Sous-types Violence Numérique";
    protected static ?string $navigationGroup = "VBG";
    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';
    protected static ?int $navigationSort = 12;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(2)->schema([
                    TextInput::make('nom')
                        ->label('Nom du sous-type')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(2),

                    Textarea::make('description')
                        ->label('Description')
                        ->rows(4)
                        ->columnSpan(2),

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
                Tables\Columns\TextColumn::make('nom')
                    ->label('Nom')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(80)
                    ->wrap(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Statut')
                    ->formatStateUsing(fn ($state) => $state ? 'Actif' : 'Inactif')
                    ->colors([
                        'success' => fn ($state) => $state === true,
                        'danger' => fn ($state) => $state === false,
                    ]),

                Tables\Columns\TextColumn::make('alertes_count')
                    ->label('Nombre d\'alertes')
                    ->counts('alertes')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
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
            'index' => Pages\ListSousTypeViolenceNumeriques::route('/'),
            'create' => Pages\CreateSousTypeViolenceNumerique::route('/create'),
            'edit' => Pages\EditSousTypeViolenceNumerique::route('/{record}/edit'),
        ];
    }
}
