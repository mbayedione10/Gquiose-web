<?php

namespace App\Filament\Resources\MenstrualCycleResource\RelationManagers;

use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;

class SymptomsRelationManager extends RelationManager
{
    protected static string $relationship = 'symptoms';

    protected static ?string $recordTitleAttribute = 'symptom_date';

    protected static ?string $title = 'Symptômes';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\DatePicker::make('symptom_date')
                ->label('Date')
                ->required(),

            Forms\Components\Select::make('pain_level')
                ->label('Niveau de douleur')
                ->options([
                    '1' => '1 - Très léger',
                    '2' => '2 - Léger',
                    '3' => '3 - Modéré',
                    '4' => '4 - Intense',
                    '5' => '5 - Très intense',
                ]),

            Forms\Components\TagsInput::make('physical_symptoms')
                ->label('Symptômes physiques')
                ->placeholder('Ex: crampes, maux de tête...'),

            Forms\Components\TagsInput::make('mood')
                ->label('Humeur')
                ->placeholder('Ex: irritable, joyeuse...'),

            Forms\Components\Select::make('discharge_type')
                ->label('Type de pertes')
                ->options([
                    'aucune' => 'Aucune',
                    'legere' => 'Légère',
                    'moderee' => 'Modérée',
                    'abondante' => 'Abondante',
                ]),

            Forms\Components\TextInput::make('temperature')
                ->label('Température (°C)')
                ->numeric()
                ->step(0.1),

            Forms\Components\Toggle::make('sexual_activity')
                ->label('Activité sexuelle'),

            Forms\Components\Toggle::make('contraception_used')
                ->label('Contraception utilisée'),

            Forms\Components\Textarea::make('notes')
                ->label('Notes'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('symptom_date')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('pain_level')
                    ->label('Douleur')
                    ->colors([
                        'success' => '1',
                        'success' => '2',
                        'warning' => '3',
                        'danger' => '4',
                        'danger' => '5',
                    ]),

                Tables\Columns\TextColumn::make('temperature')
                    ->label('Temp.')
                    ->suffix('°C'),

                Tables\Columns\IconColumn::make('sexual_activity')
                    ->label('AS')
                    ->boolean(),

                Tables\Columns\TextColumn::make('discharge_type')
                    ->label('Pertes'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
}
