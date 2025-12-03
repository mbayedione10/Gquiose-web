<?php

namespace App\Filament\Resources\EvaluationStatsResource\Pages;

use App\Filament\Resources\EvaluationStatsResource;
use Filament\Actions\Action;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\Page;
use App\Models\Evaluation;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\EvaluationStatsExport;

class ViewEvaluationStats extends Page
{
    protected static string $resource = EvaluationStatsResource::class;
    protected static string $view = 'filament.resources.evaluation-stats.dashboard';

    public $dateDebut;
    public $dateFin;
    public $contexte = 'all';
    public $periode = '30';

    public function mount(): void
    {
        $this->dateFin = now()->format('Y-m-d');
        $this->dateDebut = now()->subDays(30)->format('Y-m-d');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            EvaluationStatsResource\Widgets\GlobalStatsWidget::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            EvaluationStatsResource\Widgets\QuestionChartWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportPdf')
                ->label('Exporter PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->action('exportToPdf'),

            Action::make('exportExcel')
                ->label('Exporter Excel')
                ->icon('heroicon-o-table-cells')
                ->color('success')
                ->action('exportToExcel'),

            Action::make('filtres')
                ->label('Filtrer')
                ->icon('heroicon-o-funnel')
                ->form([
                    Select::make('periode')
                        ->label('Période prédéfinie')
                        ->options([
                            '7' => '7 derniers jours',
                            '30' => '30 derniers jours',
                            '90' => '90 derniers jours',
                            '365' => 'Année en cours',
                            'custom' => 'Personnalisée',
                        ])
                        ->default('30')
                        ->reactive(),

                    DatePicker::make('dateDebut')
                        ->label('Date de début')
                        ->default(now()->subDays(30))
                        ->visible(fn ($get) => $get('periode') === 'custom'),

                    DatePicker::make('dateFin')
                        ->label('Date de fin')
                        ->default(now())
                        ->visible(fn ($get) => $get('periode') === 'custom'),

                    Select::make('contexte')
                        ->label('Contexte')
                        ->options([
                            'all' => 'Tous',
                            'quiz' => 'Quiz',
                            'article' => 'Article',
                            'structure' => 'Structure',
                            'generale' => 'Évaluation générale',
                            'alerte' => 'Alerte',
                        ])
                        ->default('all'),
                ])
                ->action(function (array $data) {
                    $this->periode = $data['periode'];
                    $this->contexte = $data['contexte'];

                    if ($data['periode'] === 'custom') {
                        $this->dateDebut = $data['dateDebut'];
                        $this->dateFin = $data['dateFin'];
                    } else {
                        $this->dateFin = now()->format('Y-m-d');
                        $this->dateDebut = now()->subDays((int) $data['periode'])->format('Y-m-d');
                    }
                }),
        ];
    }

    public function exportToPdf()
    {
        $evaluations = $this->getFilteredEvaluations();
        $stats = $this->getStats();
        $evolution = $this->getEvolution();

        $pdf = Pdf::loadView('exports.evaluation-stats-pdf', [
            'evaluations' => $evaluations,
            'stats' => $stats,
            'dateDebut' => $this->dateDebut,
            'dateFin' => $this->dateFin,
            'contexte' => $this->contexte,
            'evolution' => $evolution,
        ]);

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'statistiques-evaluations-' . now()->format('Y-m-d') . '.pdf'
        );
    }

    public function exportToExcel()
    {
        return Excel::download(
            new EvaluationStatsExport(
                $this->getFilteredEvaluations(),
                $this->getStats(),
                $this->dateDebut,
                $this->dateFin,
                $this->contexte
            ),
            'statistiques-evaluations-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    private function getFilteredEvaluations()
    {
        $query = Evaluation::query()
            ->whereBetween('created_at', [
                Carbon::parse($this->dateDebut)->startOfDay(),
                Carbon::parse($this->dateFin)->endOfDay(),
            ]);

        if ($this->contexte !== 'all') {
            $query->where('contexte', $this->contexte);
        }

        return $query->get();
    }

    private function getStats()
    {
        $evaluations = $this->getFilteredEvaluations();

        return [
            'total' => $evaluations->count(),
            'score_moyen' => $evaluations->avg('score_global') ? round($evaluations->avg('score_global'), 2) : 0,
            'par_contexte' => $evaluations->groupBy('contexte')->map->count(),
        ];
    }

    private function getEvolution()
    {
        return Evaluation::selectRaw('DATE(created_at) as date, COUNT(*) as total, AVG(score_global) as avg_score')
            ->whereBetween('created_at', [
                Carbon::parse($this->dateDebut)->startOfDay(),
                Carbon::parse($this->dateFin)->endOfDay(),
            ])
            ->when($this->contexte !== 'all', fn($q) => $q->where('contexte', $this->contexte))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }
}