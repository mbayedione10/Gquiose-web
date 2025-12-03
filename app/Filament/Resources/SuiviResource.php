<?php

namespace App\Filament\Resources;
use Filament\Resources\Resource;
use App\Models\Suivi;
use Filament\{Tables, Forms};
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Filters\DateRangeFilter;
use App\Filament\Resources\SuiviResource\Pages;
class SuiviResource extends Resource
{
    protected static ?string $model = Suivi::class;
    protected static bool $shouldRegisterNavigation = false;
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationLabel = "Suivis";
    protected static ?string $navigationGroup = "VBG";
    protected static ?string $navigationIcon = 'heroicon-o-check';
    protected static ?int $navigationSort = 11;
    public static function form(\Filament\Forms\Form $form): Filament\Forms\Form
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
                    RichEditor::make('observation')
                        ->rules(['max:255', 'string'])
                        ->required()
                        ->placeholder('Observation')
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),
                    Select::make('alerte_id')
                        ->rules(['exists:alertes,id'])
                        ->required()
                        ->relationship('alerte', 'ref')
                        ->searchable()
                        ->placeholder('Alerte')
                        ->columnSpan([
                            'default' => 12,
                            'md' => 12,
                            'lg' => 12,
                        ]),
                ]),
            ]),
        ]);
    }
    public static function table(\Filament\Tables\Table $table): Filament\Tables\Table
    {
        return $table
            ->poll('60s')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->toggleable()
                    ->searchable(true, null, true)
                    ->limit(50),
                Tables\Columns\TextColumn::make('observation')
                    ->toggleable()
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('alerte.ref')
                    ->toggleable()
                    ->limit(50),
            ])
            ->filters([
                DateRangeFilter::make('created_at'),
                SelectFilter::make('alerte_id')
                    ->relationship('alerte', 'ref')
                    ->indicator('Alerte')
                    ->multiple()
                    ->label('Alerte'),
            ]);
    }
    public static function getRelations(): array
    {
        return [];
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSuivis::route('/'),
            'create' => Pages\CreateSuivi::route('/create'),
            'view' => Pages\ViewSuivi::route('/{record}'),
            'edit' => Pages\EditSuivi::route('/{record}/edit'),
        ];
    }
}
