<?php

namespace App\Filament\Resources\EvaluationResource\Pages;

use App\Filament\Resources\EvaluationResource;
use App\Exports\EvaluationReponsesExport;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Actions;
use Maatwebsite\Excel\Facades\Excel;

class ListEvaluations extends ListRecords
{
    protected static string $resource = EvaluationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\Action::make('exportExcel')
                ->label('Exporter en Excel')
                ->icon('heroicon-o-document-download')
                ->color('success')
                ->action(function () {
                    return $this->exportToExcel();
                }),
        ];
    }

    public function exportToExcel()
    {
        $evaluations = $this->getFilteredTableQuery()
            ->with(['utilisateur', 'reponsesDetails.questionEvaluation'])
            ->get();

        return Excel::download(
            new EvaluationReponsesExport($evaluations),
            'evaluations-reponses-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}