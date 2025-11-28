
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenstrualCycleResource\Pages;
use App\Models\MenstrualCycle;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;

class MenstrualCycleResource extends Resource
{
    protected static ?string $model = MenstrualCycle::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar';

    protected static ?string $navigationLabel = 'Cycles Menstruels';

    protected static ?string $pluralLabel = 'Cycles Menstruels';

    protected static ?string $navigationGroup = 'Santé';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make()->schema([
                Grid::make(['default' => 2])->schema([
                    Forms\Components\Select::make('utilisateur_id')
                        ->relationship('utilisateur', 'nom')
                        ->label('Utilisatrice')
                        ->searchable()
                        ->required()
                        ->columnSpan(2),

                    Forms\Components\DatePicker::make('period_start_date')
                        ->label('Début des règles')
                        ->required(),

                    Forms\Components\DatePicker::make('period_end_date')
                        ->label('Fin des règles'),

                    Forms\Components\TextInput::make('cycle_length')
                        ->label('Durée du cycle (jours)')
                        ->numeric(),

                    Forms\Components\TextInput::make('period_length')
                        ->label('Durée des règles (jours)')
                        ->numeric(),

                    Forms\Components\Select::make('flow_intensity')
                        ->label('Intensité du flux')
                        ->options([
                            'leger' => 'Léger',
                            'modere' => 'Modéré',
                            'abondant' => 'Abondant',
                        ]),

                    Forms\Components\Toggle::make('is_active')
                        ->label('Cycle actif')
                        ->default(true),

                    Forms\Components\DatePicker::make('next_period_prediction')
                        ->label('Prédiction prochaines règles')
                        ->disabled(),

                    Forms\Components\DatePicker::make('ovulation_prediction')
                        ->label('Prédiction ovulation')
                        ->disabled(),

                    Forms\Components\DatePicker::make('fertile_window_start')
                        ->label('Début fenêtre fertile')
                        ->disabled(),

                    Forms\Components\DatePicker::make('fertile_window_end')
                        ->label('Fin fenêtre fertile')
                        ->disabled(),

                    Forms\Components\Textarea::make('notes')
                        ->label('Notes')
                        ->columnSpan(2),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('utilisateur.nom')
                    ->label('Utilisatrice')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('period_start_date')
                    ->label('Début des règles')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('period_end_date')
                    ->label('Fin des règles')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('cycle_length')
                    ->label('Durée cycle')
                    ->suffix(' j'),

                Tables\Columns\BadgeColumn::make('flow_intensity')
                    ->label('Flux')
                    ->colors([
                        'success' => 'leger',
                        'warning' => 'modere',
                        'danger' => 'abondant',
                    ]),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Actif')
                    ->boolean(),

                Tables\Columns\TextColumn::make('next_period_prediction')
                    ->label('Prochaines règles')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Cycle actif')
                    ->placeholder('Tous')
                    ->trueLabel('Actifs')
                    ->falseLabel('Inactifs'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('period_start_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            MenstrualCycleResource\RelationManagers\SymptomsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenstrualCycles::route('/'),
            'create' => Pages\CreateMenstrualCycle::route('/create'),
            'view' => Pages\ViewMenstrualCycle::route('/{record}'),
            'edit' => Pages\EditMenstrualCycle::route('/{record}/edit'),
        ];
    }
}
