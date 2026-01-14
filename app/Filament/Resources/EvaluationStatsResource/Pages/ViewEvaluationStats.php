<?php

namespace App\Filament\Resources\EvaluationStatsResource\Pages;

use App\Exports\EvaluationStatsExport;
use App\Filament\Resources\EvaluationStatsResource;
use App\Filament\Widgets\AgeRangeStatsWidget;
use App\Models\Evaluation;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\Page;
use Maatwebsite\Excel\Facades\Excel;

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
            AgeRangeStatsWidget::class,
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
        $dateDebut = $this->dateDebut;
        $dateFin = $this->dateFin;
        $contexte = $this->contexte;
        $evolution = $this->getEvolution();
        $ageRangeStats = $this->getAgeRangeStats();

        $pdf = Pdf::loadView('exports.evaluation-stats-pdf', compact(
            'evaluations',
            'stats',
            'dateDebut',
            'dateFin',
            'contexte',
            'evolution',
            'ageRangeStats'
        ));

        return response()->streamDownload(
            fn () => print ($pdf->output()),
            'statistiques-evaluations-'.now()->format('Y-m-d').'.pdf'
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
                $this->contexte,
                $this->getAgeRangeStats()
            ),
            'statistiques-evaluations-'.now()->format('Y-m-d').'.xlsx'
        );
    }

    public function getFilteredEvaluations()
    {
        $query = Evaluation::whereBetween('created_at', [
            Carbon::parse($this->dateDebut)->startOfDay(),
            Carbon::parse($this->dateFin)->endOfDay(),
        ]);

        if ($this->contexte !== 'all') {
            $query->where('contexte', $this->contexte);
        }

        return $query->get();
    }

    public function getStats()
    {
        $evaluations = $this->getFilteredEvaluations();

        return [
            'total' => $evaluations->count(),
            'score_moyen' => round($evaluations->avg('score_global'), 2),
            'par_contexte' => $evaluations->groupBy('contexte')->map->count(),
            'evolution' => $this->getEvolution(),
        ];
    }

    protected function getEvolution()
    {
        return Evaluation::selectRaw('DATE(created_at) as date, COUNT(*) as total, AVG(score_global) as avg_score')
            ->whereBetween('created_at', [
                Carbon::parse($this->dateDebut)->startOfDay(),
                Carbon::parse($this->dateFin)->endOfDay(),
            ])
            ->when($this->contexte !== 'all', fn ($q) => $q->where('contexte', $this->contexte))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    public function getAgeRangeStats(): array
    {
        $currentYear = now()->year;

        $tranches = [
            'moins_15' => ['label' => 'Moins de 15 ans', 'min_age' => 0, 'max_age' => 14, 'dob_value' => '-15 ans'],
            '15_17' => ['label' => '15-17 ans', 'min_age' => 15, 'max_age' => 17, 'dob_value' => '15-17 ans'],
            '18_24' => ['label' => '18-24 ans', 'min_age' => 18, 'max_age' => 24, 'dob_value' => '18-24 ans'],
            '25_29' => ['label' => '25-29 ans', 'min_age' => 25, 'max_age' => 29, 'dob_value' => '25-29 ans'],
            '30_35' => ['label' => '30-35 ans', 'min_age' => 30, 'max_age' => 35, 'dob_value' => '30-35 ans'],
            'plus_35' => ['label' => 'Plus de 35 ans', 'min_age' => 36, 'max_age' => 200, 'dob_value' => '+35 ans'],
        ];

        $results = [];
        $total = 0;

        foreach ($tranches as $key => $config) {
            $minYear = $currentYear - $config['max_age'];
            $maxYear = $currentYear - $config['min_age'];

            $countDynamic = \App\Models\Utilisateur::whereNotNull('anneedenaissance')
                ->where('anneedenaissance', '>', 0)
                ->whereBetween('anneedenaissance', [$minYear, $maxYear])
                ->count();

            $countFallback = \App\Models\Utilisateur::where(function ($query) {
                $query->whereNull('anneedenaissance')
                    ->orWhere('anneedenaissance', 0);
            })
                ->where('dob', $config['dob_value'])
                ->count();

            $count = $countDynamic + $countFallback;
            $total += $count;
            $results[] = ['label' => $config['label'], 'count' => $count];
        }

        $sansAge = \App\Models\Utilisateur::where(function ($query) {
            $query->whereNull('anneedenaissance')
                ->orWhere('anneedenaissance', 0);
        })
            ->where(function ($query) {
                $query->whereNull('dob')
                    ->orWhere('dob', '');
            })
            ->count();

        if ($sansAge > 0) {
            $results[] = ['label' => 'Non renseigné', 'count' => $sansAge];
            $total += $sansAge;
        }

        return ['tranches' => $results, 'total' => $total];
    }
}
