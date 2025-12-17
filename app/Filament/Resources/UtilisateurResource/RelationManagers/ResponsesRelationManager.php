<?php

namespace App\Filament\Resources\UtilisateurResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\BelongsToSelect;
use Filament\Tables\Filters\MultiSelectFilter;
use Filament\Resources\RelationManagers\RelationManager;

class ResponsesRelationManager extends RelationManager
{
    protected static string $relationship = 'responses';

    protected static ?string $recordTitleAttribute = 'reponse';

    public function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(['default' => 0])->schema([
                Select::make('question_id')
                    ->rules(['exists:questions,id'])
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
                    ->placeholder('Reponse')
                    ->columnSpan([
                        'default' => 12,
                        'md' => 12,
                        'lg' => 12,
                    ]),

                Toggle::make('isValid')
                    ->rules(['boolean'])
                    ->columnSpan([
                        'default' => 12,
                        'md' => 12,
                        'lg' => 12,
                    ]),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('question.name')
                    ->label("Question ")
                    ->limit(50)
                    ->url(fn ($record) => \App\Filament\Resources\ResponseResource::getUrl('view', ['record' => $record])),

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
            ]);
    }
}
