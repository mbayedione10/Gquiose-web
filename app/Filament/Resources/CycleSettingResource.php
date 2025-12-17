<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CycleSettingResource\Pages;
use App\Models\CycleSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;

class CycleSettingResource extends Resource
{
    protected static ?string $model = CycleSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Paramètres Cycle';

    protected static ?string $pluralLabel = 'Paramètres Cycle';

    protected static ?string $navigationGroup = 'Santé';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                Grid::make(['default' => 2])->schema([
                    Forms\Components\Select::make('utilisateur_id')
                        ->relationship('utilisateur', 'nom')
                        ->label('Utilisatrice')
                        ->searchable()
                        ->required()
                        ->columnSpan(2),

                    Forms\Components\TextInput::make('average_cycle_length')
                        ->label('Durée moyenne du cycle (jours)')
                        ->numeric()
                        ->required()
                        ->default(28)
                        ->minValue(21)
                        ->maxValue(45),

                    Forms\Components\TextInput::make('average_period_length')
                        ->label('Durée moyenne des règles (jours)')
                        ->numeric()
                        ->required()
                        ->default(5)
                        ->minValue(2)
                        ->maxValue(10),

                    Forms\Components\Toggle::make('track_temperature')
                        ->label('Suivre la température')
                        ->default(false),

                    Forms\Components\Toggle::make('track_symptoms')
                        ->label('Suivre les symptômes')
                        ->default(true),

                    Forms\Components\Toggle::make('track_mood')
                        ->label('Suivre l\'humeur')
                        ->default(true),

                    Forms\Components\Toggle::make('track_sexual_activity')
                        ->label('Suivre l\'activité sexuelle')
                        ->default(false),

                    Forms\Components\Toggle::make('notifications_enabled')
                        ->label('Notifications activées')
                        ->default(true),
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

                Tables\Columns\TextColumn::make('average_cycle_length')
                    ->label('Cycle moyen')
                    ->suffix(' j')
                    ->sortable(),

                Tables\Columns\TextColumn::make('average_period_length')
                    ->label('Règles moy.')
                    ->suffix(' j')
                    ->sortable(),

                Tables\Columns\IconColumn::make('track_temperature')
                    ->label('Temp.')
                    ->boolean(),

                Tables\Columns\IconColumn::make('track_symptoms')
                    ->label('Symptômes')
                    ->boolean(),

                Tables\Columns\IconColumn::make('track_mood')
                    ->label('Humeur')
                    ->boolean(),

                Tables\Columns\IconColumn::make('track_sexual_activity')
                    ->label('Act. sex.')
                    ->boolean(),

                Tables\Columns\IconColumn::make('notifications_enabled')
                    ->label('Notif.')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('notifications_enabled')
                    ->label('Notifications')
                    ->placeholder('Tous')
                    ->trueLabel('Activées')
                    ->falseLabel('Désactivées'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCycleSettings::route('/'),
            'create' => Pages\CreateCycleSetting::route('/create'),
            'edit' => Pages\EditCycleSetting::route('/{record}/edit'),
        ];
    }
}
