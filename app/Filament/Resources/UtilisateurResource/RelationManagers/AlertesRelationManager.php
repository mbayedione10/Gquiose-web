<?php

namespace App\Filament\Resources\UtilisateurResource\RelationManagers;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
class AlertesRelationManager extends RelationManager
{
    protected static string $relationship = 'alertes';
    protected static ?string $recordTitleAttribute = 'id';
    protected function canCreate(): bool
    {
        return false;
    }
    protected function canEdit(Model $record): bool
    {
        return false;
    }
    protected function canDelete(Model $record): bool
    {
        return false;
    }
    protected function canDeleteAny(): bool
    {
        return false;
    }
    public function form(\Filament\Forms\Form $form): Filament\Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('id')
                    ->required()
                    ->maxLength(255),
            ]);
    }
    public function table(\Filament\Tables\Table $table): Filament\Tables\Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('ref')
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make("utilisateur.name")
                    ->label("Signalée par")
                    ->sortable(),
                Tables\Columns\TextColumn::make("type")
                    ->label("Type")
                    ->searchable()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('etat')
                    ->label("État")
                    ->colors([
                        'warning' => static fn ($state): bool => $state === 'Non approuvée',
                        'success' => static fn ($state): bool => $state === 'Confirmée',
                        'danger' => static fn ($state): bool => $state === 'Rejetée',
                    ])
                    ->searchable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('description')
                    ->label("Information")
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label("Signalée ")
                    ->searchable()
                    ->date("d F Y H:i")
                    ->limit(50),
            ]);
    }
}
