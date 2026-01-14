<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RubriqueResource\Pages;
use App\Models\Rubrique;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RubriqueResource extends Resource
{
    protected static ?string $model = Rubrique::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Rubriques';

    protected static ?string $navigationGroup = 'Blog';

    //protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?int $navigationSort = 31;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([

                TextInput::make('name')
                    ->label('Nom')
                    ->rules(['max:255', 'string'])
                    ->required()
                    ->unique(
                        'rubriques',
                        'name',
                        fn (?Rubrique $record) => $record
                    )
                    ->placeholder('Nom de la rubrique')
                    ->columnSpan([
                        'default' => 12,
                        'md' => 12,
                        'lg' => 12,
                    ]),

                Toggle::make('status')
                    ->label('Statut')
                    ->rules(['boolean'])
                    ->required()
                    ->columnSpan([
                        'default' => 12,
                        'md' => 12,
                        'lg' => 12,
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
                    ->label('Nom')
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('articles_count')
                    ->label('Articles')
                    ->counts('articles'),

                Tables\Columns\ToggleColumn::make('status')
                    ->label('Statut'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RubriqueResource\RelationManagers\ArticlesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRubriques::route('/'),
            'create' => Pages\CreateRubrique::route('/create'),
            'view' => Pages\ViewRubrique::route('/{record}'),
            'edit' => Pages\EditRubrique::route('/{record}/edit'),
        ];
    }
}
