<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AlerteResource\Widgets\AlertOverview;
use App\Models\Alerte;
use Illuminate\Console\View\Components\Alert;
use Filament\{Notifications\Notification, Tables, Forms};
use Filament\Resources\{Form, Table, Resource};
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Filters\DateRangeFilter;
use App\Filament\Resources\AlerteResource\Pages;

class AlerteResource extends Resource
{
    protected static ?string $model = Alerte::class;

    protected static ?string $recordTitleAttribute = 'ref';

    protected static ?string $navigationLabel = "Alertes";
    protected static ?string $navigationGroup = "VBG";
    protected static ?string $navigationIcon = 'heroicon-o-bell';
    protected static ?int $navigationSort = 10;


    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make()->schema([
                Grid::make(['default' => 0])->schema([
                    TextInput::make('ref')
                        ->rules(['max:255', 'string'])
                        ->required()
                        ->unique(
                            'alertes',
                            'ref',
                            fn(?Model $record) => $record
                        )
                        ->placeholder('Ref')
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),


                    Select::make('type')
                        ->rules(['exists:type_alertes,id'])
                        ->required()
                        ->relationship('typeAlerte', 'name')
                        ->searchable()
                        ->placeholder('Type Alerte')
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),

                    TextInput::make('etat')
                        ->rules(['max:255', 'string'])
                        ->required()
                        ->placeholder('Etat')
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),

                    Select::make('ville_id')
                        ->rules(['exists:villes,id'])
                        ->required()
                        ->relationship('ville', 'name')
                        ->searchable()
                        ->placeholder('Ville')
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('60s')
            ->columns([
                Tables\Columns\TextColumn::make('ref')

                    ->searchable()
                    ->limit(50),


                Tables\Columns\TextColumn::make("utilisateur.name")
                    ->label("Signalée par")
                    ->sortable(),

                Tables\Columns\TextColumn::make("type")
                    ->label("Type")
                    ->searchable()
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('etat')
                    ->label("État")
                    ->colors([
                        'warning' => static fn ($state): bool => $state === 'Non approuvée',
                        'success' => static fn ($state): bool => $state === 'Confirmée',
                        'danger' => static fn ($state): bool => $state === 'Rejetée',
                    ])
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('description')
                    ->label("Information")
                    ->searchable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label("Signalée ")
                    ->searchable()
                    ->date("d F Y H:i")
                    ->limit(50),


            ])
            ->actions([
                Tables\Actions\Action::make('confirmation')
                    ->label("Confirmez")
                    ->requiresConfirmation()
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->visible(function (Alerte $record){
                        return $record->etat == "Non approuvée";
                    })
                    ->modalHeading("Confirmation")
                    ->modalSubheading("Voulez-vous vraiement confirmer cette alerte ?")
                    ->action(function (Alerte $record){

                        $record->etat = "Confirmée";
                        $record->save();

                        Notification::make()
                            ->title("Information")
                            ->body("L'alerte qui a pour référence **" .$record->ref. "** vient d'être confirmée")
                            ->send();
                    }),

                Tables\Actions\Action::make('rejeter')
                    ->label("Rejetez")
                    ->requiresConfirmation()
                    ->icon('heroicon-o-x')
                    ->color('danger')
                    ->visible(function (Alerte $record){
                        return $record->etat == "Non approuvée";
                    })
                    ->modalHeading("Confirmation")
                    ->modalSubheading("Voulez-vous vraiment rejeter cette alerte ?")
                    ->action(function (Alerte $record){

                        $record->etat = "Rejetée";
                        $record->save();

                        Notification::make()
                            ->title("Information")
                            ->success()
                            ->body("L'alerte qui a pour référence **" .$record->ref. "** vient d'être rejetée")
                            ->send();
                    }),

                Tables\Actions\Action::make('description')
                    ->label("Ajouter des détails")
                    ->requiresConfirmation()
                    ->icon('heroicon-o-plus-circle')
                    ->modalHeading("Détails")
                    ->modalSubheading("Rajouter toutes les informations concernant cette alerte ")
                    ->form([
                        Forms\Components\Textarea::make('description')
                            ->label("Information")
                            ->placeholder("Saisissez ici les informations")
                            ->default(function (Alerte $record){
                                return $record->description;
                            })
                            ->required()
                    ])
                    ->action(function (Alerte $record, array $data){

                        $record->description = $data['description'];
                        $record->save();

                        Notification::make()
                            ->title("Information")
                            ->success()
                            ->body("Une nouvelle information vient d'être rajoutée")
                            ->send();
                    }),


            ])
            ->filters([

            ]);
    }

    public static function getWidgets(): array
    {
        return [
            AlertOverview::class
        ];
    }

    public static function getRelations(): array
    {
        return [AlerteResource\RelationManagers\SuivisRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAlertes::route('/'),
            'create' => Pages\CreateAlerte::route('/create'),
            'view' => Pages\ViewAlerte::route('/{record}'),
            'edit' => Pages\EditAlerte::route('/{record}/edit'),
        ];
    }
}
