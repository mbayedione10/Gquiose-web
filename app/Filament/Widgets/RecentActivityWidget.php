<?php

namespace App\Filament\Widgets;
use App\Models\Alerte;
use App\Models\Utilisateur;
use App\Models\Evaluation;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
class RecentActivityWidget extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    protected function getTableQuery(): Builder
    {
        return Alerte::query()
            ->with(['utilisateur', 'ville'])
            ->latest()
            ->limit(10);
    }
    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('ref')
                ->label('Référence')
                ->searchable()
                ->sortable(),
            
            Tables\Columns\TextColumn::make('utilisateur.name')
                ->label('Utilisateur')
                ->searchable(),
            
            Tables\Columns\TextColumn::make('ville.name')
                ->label('Ville')
                ->searchable(),
            
            Tables\Columns\BadgeColumn::make('etat')
                ->label('État')
                ->colors([
                    'warning' => 'En attente',
                    'success' => 'Confirmée',
                    'danger' => 'Rejetée',
                ]),
            
            Tables\Columns\TextColumn::make('created_at')
                ->label('Date')
                ->dateTime('d/m/Y H:i')
                ->sortable(),
        ];
    }
    protected function getTableHeading(): string
    {
        return 'Alertes Récentes';
    }
}
