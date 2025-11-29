<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EvaluationStatsExport implements FromCollection, WithHeadings, WithStyles, WithTitle
{
    protected $evaluations;
    protected $stats;

    public function __construct($evaluations, $stats)
    {
        $this->evaluations = $evaluations;
        $this->stats = $stats;
    }

    public function collection()
    {
        $data = new Collection();

        $data->push(['STATISTIQUES GLOBALES', '', '', '']);
        $data->push(['Total Évaluations', $this->stats['total'], '', '']);
        $data->push(['Score Moyen Global', $this->stats['score_moyen'] . '/5', '', '']);
        $data->push(['', '', '', '']);

        $data->push(['RÉPARTITION PAR TYPE', '', '', '']);
        foreach ($this->stats['par_contexte'] as $contexte => $count) {
            $data->push([ucfirst($contexte), $count, '', '']);
        }
        $data->push(['', '', '', '']);

        $data->push(['DÉTAIL DES ÉVALUATIONS', '', '', '']);
        $data->push(['ID', 'Contexte', 'Score Global', 'Date']);

        foreach ($this->evaluations as $evaluation) {
            $data->push([
                $evaluation->id,
                ucfirst($evaluation->contexte),
                $evaluation->score_global . '/5',
                $evaluation->created_at->format('d/m/Y H:i'),
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
            5 => ['font' => ['bold' => true, 'size' => 14]],
        ];
    }

    public function title(): string
    {
        return 'Statistiques Évaluations';
    }
}
