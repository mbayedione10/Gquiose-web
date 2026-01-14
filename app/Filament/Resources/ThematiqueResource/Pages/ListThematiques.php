<?php

namespace App\Filament\Resources\ThematiqueResource\Pages;

use App\Filament\Resources\ThematiqueChartResource\Widgets\QuestionThematiqueChart;
use App\Filament\Resources\ThematiqueResource;
use App\Filament\Traits\HasDescendingOrder;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListThematiques extends ListRecords
{
    use HasDescendingOrder;

    protected static string $resource = ThematiqueResource::class;

    protected static ?string $title = 'Liste des thématiques';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nouvelle thématique')
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            //QuestionThematiqueChart::class,
            //ThematiqueResource\Widgets\TrueResponsePerThematiqueChart::class
        ];
    }
}
