<?php

namespace App\Filament\Resources;
use Filament\Resources\Resource;
use App\Models\Response;
use Illuminate\Database\Eloquent\Model;
use Filament\{Tables, Forms};
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Filters\DateRangeFilter;
use App\Filament\Resources\ResponseResource\Pages;
class ResponseResource extends Resource
{
    protected static ?string $model = Response::class;
    protected static ?string $recordTitleAttribute = 'reponse';
    protected static ?string $navigationLabel = "Réponses";
    protected static ?string $navigationGroup = "Quiz";
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?int $navigationSort = 21;
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
    public static function canDeleteAny(): bool
    {
        return false;
    }
    public static function form(\Filament\Forms\Form $form): Filament\Forms\Form
    {
        return $form->schema([
            Card::make()->schema([
                Select::make('question_id')
                    ->rules(['exists:questions,id'])
                    ->required()
                    ->relationship('question', 'name')
                    ->searchable()
                    ->placeholder('Question')
                    ->columnSpan([
                        'default' => 12,
                        'md' => 12,
                        'lg' => 12,
                    ]),
                TextInput::make('reponse')
                    ->rules(['max:255', 'string'])
                    ->required()
                    ->placeholder('Reponse')
                    ->columnSpan([
                        'default' => 12,
                        'md' => 12,
                        'lg' => 12,
                    ]),
                Toggle::make('isValid')
                    ->rules(['boolean'])
                    ->required()
                    ->columnSpan([
                        'default' => 12,
                        'md' => 12,
                        'lg' => 12,
                    ]),
                Select::make('utilisateur_id')
                    ->rules(['exists:utilisateurs,id'])
                    ->required()
                    ->relationship('utilisateur', 'nom')
                    ->searchable()
                    ->placeholder('Utilisateur')
                    ->columnSpan([
                        'default' => 12,
                        'md' => 12,
                        'lg' => 12,
                    ]),
            ]),
        ]);
    }
    public static function table(\Filament\Tables\Table $table): Filament\Tables\Table
    {
        return $table
            ->poll('60s')
            ->columns([
                Tables\Columns\TextColumn::make('utilisateur.nom')
                    ->label("Utilisateur")
                    ->limit(50),
                Tables\Columns\TextColumn::make('question.name')
                    ->label("Question ")
                    ->limit(50),
                Tables\Columns\TextColumn::make('question.thematique.name')
                    ->label("Thématique")
                    ->limit(50),
                Tables\Columns\TextColumn::make('reponse')
                    ->label("Réponse")
                    ->searchable()
                    ->limit(50),
                Tables\Columns\IconColumn::make('isValid')
                    ->label("Trouvée")
                    ->sortable()
                    ->boolean(),
            ])
            /*->filters([
                DateRangeFilter::make('created_at'),
                SelectFilter::make('question_id')
                    ->relationship('question', 'name')
                    ->indicator('Question')
                    ->multiple()
                    ->label('Question'),
                SelectFilter::make('utilisateur_id')
                    ->relationship('utilisateur', 'nom')
                    ->indicator('Utilisateur')
                    ->multiple()
                    ->label('Utilisateur'),
            ])*/;
    }
    public static function getRelations(): array
    {
        return [];
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListResponses::route('/'),
            'create' => Pages\CreateResponse::route('/create'),
            'view' => Pages\ViewResponse::route('/{record}'),
            'edit' => Pages\EditResponse::route('/{record}/edit'),
        ];
    }
}
