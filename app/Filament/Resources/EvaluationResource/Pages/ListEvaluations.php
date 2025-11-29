<?php

namespace App\Filament\Resources\EvaluationResource\Pages;

use App\Filament\Resources\EvaluationResource;
use App\Exports\EvaluationReponsesExport;
use App\Models\Evaluation;
use Filament\Resources\Pages\ListRecords;
use Filament\Pages\Actions;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ListEvaluations extends ListRecords
{
    protected static string $resource = EvaluationResource::class;

    protected function getActions(): array
    {
        return [
            Actions\Action::make('exportPdf')
                ->label('Exporter en PDF')
                ->icon('heroicon-o-document-download')
                ->color('danger')
                ->action(function () {
                    return $this->exportToPdf();
                }),
            Actions\Action::make('exportExcel')
                ->label('Exporter en Excel')
                ->icon('heroicon-o-document-download')
                ->color('success')
                ->action(function () {
                    return $this->exportToExcel();
                }),
        ];
    }

    public function exportToPdf()
    {
        $evaluations = $this->getFilteredTableQuery()
            ->with(['utilisateur', 'reponsesDetails.questionEvaluation'])
            ->get();

        $pdf = Pdf::loadView('exports.evaluation-reponses-pdf', [
            'evaluations' => $evaluations,
        ]);

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'evaluations-reponses-' . now()->format('Y-m-d') . '.pdf'
        );
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