<?php

namespace App\Filament\Resources;

use App\Models\Structure;
use App\Models\TypeStructure;
use App\Models\Ville;
use Filament\{Tables, Forms};
use Filament\Resources\{Form, Table, Resource};
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Filters\DateRangeFilter;
use App\Filament\Resources\StructureResource\Pages;

class StructureResource extends Resource
{
    protected static ?string $model = Structure::class;


    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = "Structures sanitaires";
    protected static ?string $navigationGroup = "Santé";
    protected static ?string $navigationIcon = 'heroicon-o-office-building';
    protected static ?int $navigationSort = 40;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make()->schema([
                Grid::make(['default' => 0])->schema([


                    Select::make('type_structure_id')
                        ->label("Type de structure")
                        ->rules(['exists:type_structures,id'])
                        ->label("Choisir un type de structure")
                        ->required()
                        ->relationship('typeStructure', 'name')
                        ->searchable()
                        ->placeholder('Type Structure')
                        ->createOptionForm([
                            TextInput::make('name')
                                ->rules(['max:255', 'string'])
                                ->required()
                                ->unique(
                                    'type_structures',
                                    'name',
                                    fn(?TypeStructure $record) => $record
                                )
                                ->placeholder('Name')
                                ->columnSpan([
                                    'default' => 12,
                                    'md' => 12,
                                    'lg' => 12,
                                ]),

                            Forms\Components\FileUpload::make('icon')
                                ->maxSize(512)
                                ->image()
                                ->required()
                                ->placeholder('Icon')
                                ->columnSpan([
                                    'default' => 12,
                                    'md' => 12,
                                    'lg' => 12,
                                ]),

                            Toggle::make('status')
                                ->rules(['boolean'])
                                ->required()
                                ->columnSpan([
                                    'default' => 12,
                                    'md' => 12,
                                    'lg' => 12,
                                ]),
                        ])
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),

                    TextInput::make('name')
                        ->rules(['max:255', 'string'])
                        ->label("Nom de la structure")
                        ->required()
                        ->placeholder('Nom de la structure')
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),

                    Forms\Components\Textarea::make('description')
                        ->nullable()
                        ->label("Description de la structure")
                        ->placeholder('Description de la structure')
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),

                    TextInput::make('phone')
                        ->rules(['max:255', 'string'])
                        ->label("Téléphone")
                        ->required()
                        ->unique(
                            'structures',
                            'phone',
                            fn(?Model $record) => $record
                        )
                        ->placeholder('Numéro de téléphone')
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
                        ->createOptionForm([
                            TextInput::make('name')
                                ->rules(['max:255', 'string'])
                                ->required()
                                ->unique(
                                    'villes',
                                    'name',
                                    fn(?Ville $record) => $record
                                )
                                ->placeholder('Name')
                                ->columnSpan([
                                    'default' => 12,
                                    'md' => 12,
                                    'lg' => 12,
                                ]),

                            Toggle::make('status')
                                ->rules(['boolean'])
                                ->required()
                                ->columnSpan([
                                    'default' => 12,
                                    'md' => 12,
                                    'lg' => 12,
                                ]),
                        ])
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),

                    TextInput::make('adresse')
                        ->rules(['max:255', 'string'])
                        ->required()
                        ->placeholder('Adresse')
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),

                    TextInput::make('latitude')
                        ->label("Latitude")
                        ->required()
                        ->numeric()
                        ->placeholder('Latitude')
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),

                    TextInput::make('longitude')
                        ->label("Longitude")
                        ->required()
                        ->numeric()
                        ->placeholder('Longitude')
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),


                    Toggle::make('status')
                        ->rules(['boolean'])
                        ->required()
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
                Tables\Columns\TextColumn::make('name')
                    ->label("Structure")
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('description')
                    ->label("Offre")
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('phone')
                    ->label("Téléphone")
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('typeStructure.name')
                    ->limit(50),

                Tables\Columns\TextColumn::make('ville.name')
                    ->label("Ville")
                    ->sortable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('adresse')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\ToggleColumn::make('status'),


            ])
            ->filters([
                DateRangeFilter::make('created_at'),

                SelectFilter::make('type_structure_id')
                    ->relationship('typeStructure', 'name')
                    ->indicator('TypeStructure')
                    ->multiple()
                    ->label('TypeStructure'),

                SelectFilter::make('ville_id')
                    ->relationship('ville', 'name')
                    ->indicator('Ville')
                    ->multiple()
                    ->label('Ville'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStructures::route('/'),
            'create' => Pages\CreateStructure::route('/create'),
            'view' => Pages\ViewStructure::route('/{record}'),
            'edit' => Pages\EditStructure::route('/{record}/edit'),
        ];
    }
}
