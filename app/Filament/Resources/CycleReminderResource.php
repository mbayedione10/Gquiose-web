<?php

namespace App\Filament\Resources;

use App\Models\CycleReminder;
use App\Filament\Resources\CycleReminderResource\Pages;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TagsInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class CycleReminderResource extends Resource
{
    protected static ?string $model = CycleReminder::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static ?string $navigationLabel = 'Rappels Cycle';
    protected static ?string $pluralLabel = 'Rappels Cycle';
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

                    Select::make('reminder_type')
                        ->label('Type de rappel')
                        ->options([
                            'period_approaching' => 'Règles qui approchent',
                            'period_today' => 'Règles prévues aujourd\'hui',
                            'ovulation_approaching' => 'Ovulation qui approche',
                            'fertile_window' => 'Fenêtre de fertilité',
                            'log_symptoms' => 'Rappel de noter symptômes',
                            'pill_reminder' => 'Rappel pilule contraceptive',
                        ])
                        ->required(),

                    TimePicker::make('reminder_time')
                        ->label('Heure du rappel')
                        ->required()
                        ->default('09:00'),

                    Toggle::make('enabled')
                        ->label('Activé')
                        ->default(true),

                    TagsInput::make('days_before')
                        ->label('Jours avant')
                        ->placeholder('Ex: 2, 1')
                        ->suggestions(['1', '2', '3', '5', '7'])
                        ->columnSpan(2),
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

                BadgeColumn::make('reminder_type')
                    ->label('Type')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'period_approaching' => 'Règles approchent',
                        'period_today' => 'Règles aujourd\'hui',
                        'ovulation_approaching' => 'Ovulation approche',
                        'fertile_window' => 'Fenêtre fertile',
                        'log_symptoms' => 'Noter symptômes',
                        'pill_reminder' => 'Pilule',
                        default => $state,
                    })
                    ->colors([
                        'danger' => 'period_today',
                        'warning' => ['period_approaching', 'ovulation_approaching'],
                        'success' => 'fertile_window',
                        'info' => 'pill_reminder',
                        'secondary' => 'log_symptoms',
                    ]),

                TextColumn::make('reminder_time')
                    ->label('Heure')
                    ->time('H:i')
                    ->sortable(),

                IconColumn::make('enabled')
                    ->label('Actif')
                    ->boolean(),

                TextColumn::make('days_before')
                    ->label('Jours avant')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : '-'),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('reminder_type')
                    ->label('Type de rappel')
                    ->options([
                        'period_approaching' => 'Règles qui approchent',
                        'period_today' => 'Règles aujourd\'hui',
                        'ovulation_approaching' => 'Ovulation',
                        'fertile_window' => 'Fenêtre fertile',
                        'log_symptoms' => 'Symptômes',
                        'pill_reminder' => 'Pilule',
                    ])
                    ->multiple(),

                TernaryFilter::make('enabled')
                    ->label('Statut')
                    ->placeholder('Tous')
                    ->trueLabel('Actifs')
                    ->falseLabel('Inactifs'),
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
            'index'  => Pages\ListCycleReminders::route('/'),
            'create' => Pages\CreateCycleReminder::route('/create'),
            'edit'   => Pages\EditCycleReminder::route('/{record}/edit'),
        ];
    }
}