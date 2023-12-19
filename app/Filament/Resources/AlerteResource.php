<?php

namespace App\Filament\Resources;

use App\Models\Alerte;
use Filament\{Tables, Forms};
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
                    ->toggleable()
                    ->searchable()
                    ->limit(50),


                Tables\Columns\TextColumn::make("utilisateur.name")
                    ->label("Signalée par")
                    ->sortable(),

                Tables\Columns\TextColumn::make("type")
                    ->label("type")
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('etat')
                    ->label("État")
                    ->colors([
                        'warning' => 'Non approveée',
                        'success' => 'Approvée',
                        'danger' => 'Rejeter',
                    ])
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('created_at')
                    ->searchable()
                    ->date("d F Y H:i")
                    ->limit(50),


            ])
            ->filters([

            ]);
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
