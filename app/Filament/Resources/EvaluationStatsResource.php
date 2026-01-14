<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EvaluationStatsResource\Pages;
use Filament\Resources\Resource;
use Filament\Resources\Table;

class EvaluationStatsResource extends Resource
{
    // Set model to null as this resource is for aggregated statistics, not direct model management.
    protected static ?string $model = null;

    protected static ?string $navigationLabel = 'Statistiques';

    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static ?int $navigationSort = 1;
    // The table method is removed as this resource will have a dedicated statistics page instead of a table view.
    // public static function table(Table $table): Table
    // {
    //     // ... table definition removed
    // }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ViewEvaluationStats::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
