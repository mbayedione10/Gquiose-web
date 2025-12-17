<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Actions\Action;
use App\Exports\DashboardStatsExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Utilisateur;
use App\Models\Alerte;
use App\Models\Article;
use App\Models\Video;
use App\Models\Structure;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static string $view = 'filament.pages.dashboard';
    protected static ?int $navigationSort = -2;

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
        ];
    }

    public function exportToPdf()
    {
        $totalUtilisateurs = Utilisateur::count();
        $utilisateursActifs = Utilisateur::where('status', true)->count();
        $totalAlertes = Alerte::count();
        $alertesConfirmees = Alerte::where('etat', 'Confirmée')->count();
        $totalArticles = Article::count();
        $totalVideos = Video::count();
        $totalStructures = Structure::count();

        $alertesRecentes = Alerte::with(['utilisateur', 'ville'])
            ->latest()
            ->take(7)
            ->get();

        $alertesParType = Alerte::with('typeAlerte')
            ->get()
            ->groupBy('type_alerte_id')
            ->map(function ($alertes) {
                $typeAlerte = $alertes->first()->typeAlerte;
                return (object) [
                    'type_name' => $typeAlerte?->name ?? 'Non classifié',
                    'total' => $alertes->count(),
                ];
            })
            ->sortByDesc('total')
            ->values();

        $pdf = Pdf::loadView('exports.dashboard-stats-pdf', compact(
            'totalUtilisateurs',
            'utilisateursActifs',
            'totalAlertes',
            'alertesConfirmees',
            'totalArticles',
            'totalVideos',
            'totalStructures',
            'alertesRecentes',
            'alertesParType'
        ));

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'dashboard-stats-' . now()->format('Y-m-d') . '.pdf'
        );
    }

    public function exportToExcel()
    {
        return Excel::download(
            new DashboardStatsExport(),
            'dashboard-stats-' . now()->format('Y-m-d') . '.xlsx'
        );
    }
}
