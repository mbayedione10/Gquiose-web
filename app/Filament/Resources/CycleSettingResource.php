<?php

namespace App\Filament\Resources;

use App\Models\CycleSetting;
use App\Filament\Resources\CycleSettingResource\Pages;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;

class CycleSettingResource extends Resource
{
    protected static ?string $model = CycleSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationLabel = 'Paramètres Cycle';
    protected static ?string $pluralLabel = 'Paramètres Cycle';
    protected static ?string $navigationGroup = 'Santé';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make()->schema([
                Grid::make(2)->schema([
                    Select::make('utilisateur_id')
                        ->relationship('utilisateur', 'nom')
                        ->label('Utilisatrice')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->columnSpan(2),

                    TextInput::make('average_cycle_length')
                        ->label('Durée moyenne du cycle (jours)')
                        ->numeric()
                        ->required()
                        ->default(28)
                        ->minValue(21)
                        ->maxValue(45),

                    TextInput::make('average_period_length')
                        ->label('Durée moyenne des règles (jours)')
                        ->numeric()
                        ->required()
                        ->default(5)
                        ->minValue(2)
                        ->maxValue(10),

                    Toggle::make('track_temperature')
                        ->label('Suivre la température')
                        ->default(false),

                    Toggle::make('track_symptoms')
                        ->label('Suivre les symptômes')
                        ->default(true),

                    Toggle::make('track_mood')
                        ->label('Suivre l\'humeur')
                        ->default(true),

                    Toggle::make('track_sexual_activity')
                        ->label('Suivre l\'activité sexuelle')
                        ->default(false),

                    Toggle::make('notifications_enabled')
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
                TextColumn::make('utilisateur.nom')
                    ->label('Utilisatrice')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('average_cycle_length')
                    ->label('Cycle moyen')
                    ->suffix(' j')
                    ->sortable(),

                TextColumn::make('average_period_length')
                    ->label('Règles moy.')
                    ->suffix(' j')
                    ->sortable(),

                IconColumn::make('track_temperature')
                    ->label('Temp.')
                    ->boolean(),

                IconColumn::make('track_symptoms')
                    ->label('Symptômes')
                    ->boolean(),

                IconColumn::make('track_mood')
                    ->label('Humeur')
                    ->boolean(),

                IconColumn::make('track_sexual_activity')
                    ->label('Act. sex.')
                    ->boolean(),

                IconColumn::make('notifications_enabled')
                    ->label('Notif.')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('notifications_enabled')
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
            'index'  => Pages\ListCycleSettings::route('/'),
            'create' => Pages\CreateCycleSetting::route('/create'),
            'edit'   => Pages\EditCycleSetting::route('/{record}/edit'),
        ];
    }
}