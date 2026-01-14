
<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EvaluationStatsExport implements WithMultipleSheets
{
    protected $evaluations;

    protected $stats;

    protected $dateDebut;

    protected $dateFin;

    protected $contexte;

    protected $ageRangeStats;

    public function __construct($evaluations, $stats, $dateDebut, $dateFin, $contexte, $ageRangeStats = null)
    {
        $this->evaluations = $evaluations;
        $this->stats = $stats;
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
        $this->contexte = $contexte;
        $this->ageRangeStats = $ageRangeStats;
    }

    public function sheets(): array
    {
        $sheets = [
            new EvaluationStatsSheet($this->evaluations, $this->stats, $this->dateDebut, $this->dateFin, $this->contexte),
            new EvaluationDetailsSheet($this->evaluations),
        ];

        if ($this->ageRangeStats) {
            $sheets[] = new AgeRangeStatsSheet($this->ageRangeStats);
        }

        return $sheets;
    }
}

class EvaluationStatsSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $evaluations;

    protected $stats;

    protected $dateDebut;

    protected $dateFin;

    protected $contexte;

    public function __construct($evaluations, $stats, $dateDebut, $dateFin, $contexte)
    {
        $this->evaluations = $evaluations;
        $this->stats = $stats;
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
        $this->contexte = $contexte;
    }

    public function collection()
    {
        $data = new Collection();

        $data->push(['RAPPORT STATISTIQUES DES ÉVALUATIONS', '', '', '']);
        $data->push(['Période', \Carbon\Carbon::parse($this->dateDebut)->format('d/m/Y').' - '.\Carbon\Carbon::parse($this->dateFin)->format('d/m/Y'), '', '']);
        $data->push(['Filtre Contexte', $this->contexte !== 'all' ? ucfirst($this->contexte) : 'Tous', '', '']);
        $data->push(['Généré le', now()->format('d/m/Y à H:i'), '', '']);
        $data->push(['', '', '', '']);

        $data->push(['STATISTIQUES GLOBALES', '', '', '']);
        $data->push(['Total Évaluations', $this->stats['total'], '', '']);
        $data->push(['Score Moyen Global', $this->stats['score_moyen'].'/5', '', '']);
        $data->push(['Types de Formulaires', count($this->stats['par_contexte']), '', '']);
        $data->push(['', '', '', '']);

        $data->push(['RÉPARTITION PAR CONTEXTE', '', '', '']);
        $data->push(['Contexte', 'Nombre', 'Pourcentage', 'Score Moyen']);
        foreach ($this->stats['par_contexte'] as $contexte => $count) {
            $percentage = $this->stats['total'] > 0 ? round(($count / $this->stats['total']) * 100, 1) : 0;
            $scoreMoyen = $this->evaluations->where('contexte', $contexte)->avg('score_global');
            $data->push([
                ucfirst($contexte),
                $count,
                $percentage.'%',
                $scoreMoyen ? number_format($scoreMoyen, 2) : 'N/A',
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
            6 => ['font' => ['bold' => true, 'size' => 12]],
            11 => ['font' => ['bold' => true, 'size' => 12]],
            12 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Statistiques';
    }
}

class EvaluationDetailsSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $evaluations;

    public function __construct($evaluations)
    {
        $this->evaluations = $evaluations;
    }

    public function collection()
    {
        $data = new Collection();

        foreach ($this->evaluations as $evaluation) {
            $data->push([
                $evaluation->id,
                $evaluation->utilisateur ? $evaluation->utilisateur->name : 'N/A',
                ucfirst($evaluation->contexte),
                $evaluation->score_global ? $evaluation->score_global.'/5' : 'N/A',
                $evaluation->commentaire ?? '',
                $evaluation->created_at->format('d/m/Y H:i'),
            ]);
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Utilisateur',
            'Contexte',
            'Score Global',
            'Commentaire',
            'Date',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function title(): string
    {
        return 'Détails Évaluations';
    }
}

class AgeRangeStatsSheet implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $ageRangeStats;

    public function __construct($ageRangeStats)
    {
        $this->ageRangeStats = $ageRangeStats;
    }

    public function collection()
    {
        $data = new Collection();

        $data->push(['RÉPARTITION PAR TRANCHE D\'ÂGE', '', '']);
        $data->push(['Généré le', now()->format('d/m/Y à H:i'), '']);
        $data->push(['Total Utilisateurs', $this->ageRangeStats['total'], '']);
        $data->push(['', '', '']);

        foreach ($this->ageRangeStats['tranches'] as $tranche) {
            $percentage = $this->ageRangeStats['total'] > 0
                ? round(($tranche['count'] / $this->ageRangeStats['total']) * 100, 1)
                : 0;

            $data->push([
                $tranche['label'],
                $tranche['count'],
                $percentage.'%',
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
            5 => ['font' => ['bold' => true]],
        ];
    }

    public function title(): string
    {
        return 'Tranches d\'âge';
    }
}
