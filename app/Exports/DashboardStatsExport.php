
<?php

namespace App\Exports;

use App\Models\Alerte;
use App\Models\Article;
use App\Models\Structure;
use App\Models\Utilisateur;
use App\Models\Video;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DashboardStatsExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    public function collection()
    {
        $data = new Collection();

        // Statistiques globales
        $totalUtilisateurs = Utilisateur::count();
        $utilisateursActifs = Utilisateur::where('status', true)->count();
        $totalAlertes = Alerte::count();
        $alertesConfirmees = Alerte::where('etat', 'Confirmée')->count();
        $totalArticles = Article::count();
        $totalVideos = Video::count();
        $totalStructures = Structure::count();

        $data->push(['TABLEAU DE BORD - STATISTIQUES GLOBALES', '', '', '']);
        $data->push(['Généré le', now()->format('d/m/Y à H:i'), '', '']);
        $data->push(['', '', '', '']);

        $data->push(['MÉTRIQUES PRINCIPALES', '', '', '']);
        $data->push(['Total Utilisateurs', $totalUtilisateurs, '', '']);
        $data->push(['Utilisateurs Actifs', $utilisateursActifs, '', '']);
        $data->push(['Total Alertes', $totalAlertes, '', '']);
        $data->push(['Alertes Confirmées', $alertesConfirmees, '', '']);
        $data->push(['Total Articles', $totalArticles, '', '']);
        $data->push(['Total Vidéos', $totalVideos, '', '']);
        $data->push(['Total Structures', $totalStructures, '', '']);
        $data->push(['', '', '', '']);

        // Distribution des alertes par type
        $data->push(['DISTRIBUTION DES ALERTES PAR TYPE', '', '', '']);
        $data->push(['Type de Violence', 'Nombre', 'Pourcentage', '']);

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

        $totalAlertesType = $alertesParType->sum('total');

        foreach ($alertesParType as $type) {
            $percentage = $totalAlertesType > 0 ? round(($type->total / $totalAlertesType * 100), 1) : 0;
            $data->push([
                $type->type_name,
                $type->total,
                $percentage.'%',
                '',
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return [];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            4 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function title(): string
    {
        return 'Dashboard Stats';
    }
}
