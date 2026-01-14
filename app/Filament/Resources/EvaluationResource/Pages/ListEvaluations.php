<?php

namespace App\Filament\Resources\EvaluationResource\Pages;

use App\Exports\EvaluationReponsesExport;
use App\Filament\Resources\EvaluationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;

class ListEvaluations extends ListRecords
{
    protected static string $resource = EvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('exportExcel')
                ->label('Exporter en Excel')
                ->icon('heroicon-o-document-arrow-down')
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
            'evaluations-reponses-'.now()->format('Y-m-d').'.xlsx'
        );
    }
}
