
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CycleReminderResource\Pages;
use App\Models\CycleReminder;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;

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
                Grid::make(['default' => 2])->schema([
                    Forms\Components\Select::make('utilisateur_id')
                        ->relationship('utilisateur', 'nom')
                        ->label('Utilisatrice')
                        ->searchable()
                        ->required()
                        ->columnSpan(2),

                    Forms\Components\Select::make('reminder_type')
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

                    Forms\Components\TimePicker::make('reminder_time')
                        ->label('Heure du rappel')
                        ->required()
                        ->default('09:00'),

                    Forms\Components\Toggle::make('enabled')
                        ->label('Activé')
                        ->default(true),

                    Forms\Components\TagsInput::make('days_before')
                        ->label('Jours avant (ex: 2,1 pour rappel 2 et 1 jour avant)')
                        ->placeholder('Ex: 2, 1')
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

                Tables\Columns\BadgeColumn::make('reminder_type')
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
                        'warning' => 'period_approaching',
                        'success' => 'ovulation_approaching',
                        'primary' => 'fertile_window',
                        'secondary' => 'log_symptoms',
                        'info' => 'pill_reminder',
                    ]),

                Tables\Columns\TextColumn::make('reminder_time')
                    ->label('Heure')
                    ->time('H:i')
                    ->sortable(),

                Tables\Columns\IconColumn::make('enabled')
                    ->label('Actif')
                    ->boolean(),

                Tables\Columns\TextColumn::make('days_before')
                    ->label('Jours avant')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', $state) : '-'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Créé le')
                    ->date('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('reminder_type')
                    ->label('Type de rappel')
                    ->options([
                        'period_approaching' => 'Règles qui approchent',
                        'period_today' => 'Règles aujourd\'hui',
                        'ovulation_approaching' => 'Ovulation',
                        'fertile_window' => 'Fenêtre fertile',
                        'log_symptoms' => 'Symptômes',
                        'pill_reminder' => 'Pilule',
                    ]),

                Tables\Filters\TernaryFilter::make('enabled')
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
            'index' => Pages\ListCycleReminders::route('/'),
            'create' => Pages\CreateCycleReminder::route('/create'),
            'edit' => Pages\EditCycleReminder::route('/{record}/edit'),
        ];
    }
}
