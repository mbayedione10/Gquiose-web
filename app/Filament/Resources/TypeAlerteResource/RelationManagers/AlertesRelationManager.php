<?php

namespace App\Filament\Resources\TypeAlerteResource\RelationManagers;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\BelongsToSelect;
use Filament\Tables\Filters\MultiSelectFilter;
class AlertesRelationManager extends RelationManager
{
    protected static string $relationship = 'alertes';
    protected static ?string $recordTitleAttribute = 'ref';
    public function form(\Filament\Forms\Form $form): Filament\Forms\Form
    {
        return $form->schema([
            Grid::make(['default' => 0])->schema([
                TextInput::make('ref')
                    ->rules(['max:255', 'string'])
                    ->unique('alertes', 'ref', fn(?Model $record) => $record)
                    ->placeholder('Ref')
                    ->columnSpan([
                        'default' => 12,
                        'md' => 12,
                        'lg' => 12,
                    ]),
                RichEditor::make('description')
                    ->rules(['max:255', 'string'])
                    ->placeholder('Description')
                    ->columnSpan([
                        'default' => 12,
                        'md' => 12,
                        'lg' => 12,
                    ]),
                TextInput::make('latitude')
                    ->rules(['numeric'])
                    ->numeric()
                    ->placeholder('Latitude')
                    ->columnSpan([
                        'default' => 12,
                        'md' => 12,
                        'lg' => 12,
                    ]),
                TextInput::make('longitude')
                    ->rules(['numeric'])
                    ->numeric()
                    ->placeholder('Longitude')
                    ->columnSpan([
                        'default' => 12,
                        'md' => 12,
                        'lg' => 12,
                    ]),
                TextInput::make('etat')
                    ->rules(['max:255', 'string'])
                    ->placeholder('Etat')
                    ->columnSpan([
                        'default' => 12,
                        'md' => 12,
                        'lg' => 12,
                    ]),
                Select::make('ville_id')
                    ->rules(['exists:villes,id'])
                    ->relationship('ville', 'name')
                    ->searchable()
                    ->placeholder('Ville')
                    ->columnSpan([
                        'default' => 12,
                        'md' => 12,
                        'lg' => 12,
                    ]),
            ]),
        ]);
    }
    public function table(\Filament\Tables\Table $table): Filament\Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('ref')->limit(50),
                Tables\Columns\TextColumn::make('description')->limit(50),
                Tables\Columns\TextColumn::make('latitude'),
                Tables\Columns\TextColumn::make('longitude'),
                Tables\Columns\TextColumn::make('typeAlerte.name')->limit(50),
                Tables\Columns\TextColumn::make('etat')->limit(50),
                Tables\Columns\TextColumn::make('ville.name')->limit(50),
            ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(
                                    Builder $query,
                                    $date
                                ): Builder => $query->whereDate(
                                    'created_at',
                                    '>=',
                                    $date
                                )
                            )
                            ->when(
                                $data['created_until'],
                                fn(
                                    Builder $query,
                                    $date
                                ): Builder => $query->whereDate(
                                    'created_at',
                                    '<=',
                                    $date
                                )
                            );
                    }),
                MultiSelectFilter::make('type_alerte_id')->relationship(
                    'typeAlerte',
                    'name'
                ),
                MultiSelectFilter::make('ville_id')->relationship(
                    'ville',
                    'name'
                ),
            ])
            ->headerActions([Tables\Actions\CreateAction::make()])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }
}
