<?php

namespace App\Filament\Resources;

use App\Models\Structure;
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
                    TextInput::make('name')
                        ->rules(['max:255', 'string'])
                        ->required()
                        ->placeholder('Name')
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),

                    RichEditor::make('description')
                        ->rules(['max:255', 'string'])
                        ->nullable()
                        ->placeholder('Description')
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),

                    TextInput::make('latitude')
                        ->rules(['numeric'])
                        ->required()
                        ->numeric()
                        ->placeholder('Latitude')
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),

                    TextInput::make('longitude')
                        ->rules(['numeric'])
                        ->required()
                        ->numeric()
                        ->placeholder('Longitude')
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),

                    TextInput::make('phone')
                        ->rules(['max:255', 'string'])
                        ->required()
                        ->unique(
                            'structures',
                            'phone',
                            fn(?Model $record) => $record
                        )
                        ->placeholder('Phone')
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),

                    Select::make('type_structure_id')
                        ->rules(['exists:type_structures,id'])
                        ->required()
                        ->relationship('typeStructure', 'name')
                        ->searchable()
                        ->placeholder('Type Structure')
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

                    TextInput::make('adresse')
                        ->rules(['max:255', 'string'])
                        ->required()
                        ->placeholder('Adresse')
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
                    ->toggleable()
                    ->searchable(true, null, true)
                    ->limit(50),
                Tables\Columns\TextColumn::make('description')
                    ->toggleable()
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('latitude')
                    ->toggleable()
                    ->searchable(true, null, true),
                Tables\Columns\TextColumn::make('longitude')
                    ->toggleable()
                    ->searchable(true, null, true),
                Tables\Columns\TextColumn::make('phone')
                    ->toggleable()
                    ->searchable(true, null, true)
                    ->limit(50),
                Tables\Columns\TextColumn::make('typeStructure.name')
                    ->toggleable()
                    ->limit(50),
                Tables\Columns\IconColumn::make('status')
                    ->toggleable()
                    ->boolean(),
                Tables\Columns\TextColumn::make('ville.name')
                    ->toggleable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('adresse')
                    ->toggleable()
                    ->searchable(true, null, true)
                    ->limit(50),
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
