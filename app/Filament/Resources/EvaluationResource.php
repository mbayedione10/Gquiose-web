<?php
namespace App\Filament\Resources;

use App\Models\Evaluation;
use Filament\Forms;
use Filament\Tables;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Forms\Components\Card;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\EvaluationResource\Pages;

class EvaluationResource extends Resource
{
    protected static ?string $model = Evaluation::class;
    protected static ?string $recordTitleAttribute = 'id';
    protected static ?string $navigationLabel = 'Évaluations reçues';
    protected static ?string $navigationGroup = 'Évaluations';
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?int $navigationSort = 31;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Card::make()->schema([
                Forms\Components\Textarea::make('reponses')
                    ->label('Réponses')
                    ->disabled()
                    ->columnSpan(12),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->poll('60s')
            ->columns([
                TextColumn::make('utilisateur.name')
                    ->label('Utilisateur')
                    ->searchable(['nom', 'prenom']),

                BadgeColumn::make('contexte')
                    ->label('Contexte')
                    ->enum([
                        'quiz' => 'Quiz',
                        'article' => 'Article',
                        'structure' => 'Structure',
                        'generale' => 'Générale',
                        'alerte' => 'Alerte',
                    ]),

                TextColumn::make('score_global')
                    ->label('Score')
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 1) . '/5' : 'N/A')
                    ->sortable(),

                TextColumn::make('commentaire')
                    ->label('Commentaire')
                    ->limit(50)
                    ->toggleable(),

                TextColumn::make('created_at')
                    ->label('Date')
                    ->date('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('contexte')
                    ->options([
                        'quiz' => 'Quiz',
                        'article' => 'Article',
                        'structure' => 'Structure',
                        'generale' => 'Générale',
                        'alerte' => 'Alerte',
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEvaluations::route('/'),
            'view'  => Pages\ViewEvaluation::route('/{record}'),
        ];
    }
}
