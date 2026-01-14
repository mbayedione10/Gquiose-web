<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ThematiqueChartResource\Widgets\QuestionThematiqueChart;
use App\Filament\Resources\ThematiqueResource\Pages;
use App\Filament\Resources\ThematiqueResource\Widgets\TrueResponsePerThematiqueChart;
use App\Models\Thematique;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ThematiqueResource extends Resource
{
    protected static ?string $model = Thematique::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'Thématiques';

    protected static ?string $navigationGroup = 'Quiz';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 22;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make()->schema([
                TextInput::make('name')
                    ->label('Thématique')
                    ->rules(['max:255', 'string'])
                    ->required()
                    ->unique(
                        'thematiques',
                        'name',
                        fn (?Thematique $record) => $record
                    )
                    ->placeholder('Nom de la thématique')
                    ->columnSpan([
                        'default' => 12,
                        'md' => 12,
                        'lg' => 12,
                    ]),

                Toggle::make('status')
                    ->label('Activée')
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

                Tables\Columns\TextColumn::make('questions_count')
                    ->label('Questions')
                    ->sortable()
                    ->counts('questions'),

                Tables\Columns\IconColumn::make('status')
                    ->label('Statut')
                    ->boolean(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ThematiqueResource\RelationManagers\QuestionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListThematiques::route('/'),
            'create' => Pages\CreateThematique::route('/create'),
            'view' => Pages\ViewThematique::route('/{record}'),
            'edit' => Pages\EditThematique::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            //QuestionThematiqueChart::class,
            TrueResponsePerThematiqueChart::class,
        ];
    }
}
