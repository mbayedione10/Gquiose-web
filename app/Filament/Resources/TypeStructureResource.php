<?php

namespace App\Filament\Resources;

use App\Models\TypeStructure;
use Filament\{Tables, Forms};
use Filament\Resources\{Form, Table, Resource};
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use App\Filament\Filters\DateRangeFilter;
use App\Filament\Resources\TypeStructureResource\Pages;

class TypeStructureResource extends Resource
{
    protected static ?string $model = TypeStructure::class;


    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = "Type de structures";
    protected static ?string $navigationGroup = "Santé";
    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?int $navigationSort = 42;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make()->schema([
                Grid::make(['default' => 0])->schema([
                    TextInput::make('name')
                        ->rules(['max:255', 'string'])
                        ->required()
                        ->unique(
                            'type_structures',
                            'name',
                            fn(?Model $record) => $record
                        )
                        ->placeholder('Name')
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),

                    TextInput::make('icon')
                        ->rules(['max:255', 'string'])
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
                Tables\Columns\TextColumn::make('icon')
                    ->toggleable()
                    ->searchable(true, null, true)
                    ->limit(50),
                Tables\Columns\IconColumn::make('status')
                    ->toggleable()
                    ->boolean(),
            ])
            ->filters([DateRangeFilter::make('created_at')]);
    }

    public static function getRelations(): array
    {
        return [
            TypeStructureResource\RelationManagers\StructuresRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTypeStructures::route('/'),
            'create' => Pages\CreateTypeStructure::route('/create'),
            'view' => Pages\ViewTypeStructure::route('/{record}'),
            'edit' => Pages\EditTypeStructure::route('/{record}/edit'),
        ];
    }
}
