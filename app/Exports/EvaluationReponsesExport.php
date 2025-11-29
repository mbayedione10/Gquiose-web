<?php

namespace App\Exports;

use App\Models\Evaluation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EvaluationReponsesExport implements FromCollection, WithHeadings, WithStyles, WithTitle
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
                'ID' => $evaluation->id,
                'Utilisateur' => $evaluation->utilisateur ? $evaluation->utilisateur->nom . ' ' . $evaluation->utilisateur->prenom : 'N/A',
                'Contexte' => ucfirst($evaluation->contexte),
                'Score Global' => $evaluation->score_global ? $evaluation->score_global . '/5' : 'N/A',
                'Commentaire' => $evaluation->commentaire ?: '',
                'Date' => $evaluation->created_at->format('d/m/Y H:i'),
            ]);

            // Ajouter les réponses détaillées
            foreach ($evaluation->reponsesDetails as $reponse) {
                $data->push([
                    '',
                    'Question: ' . $reponse->questionEvaluation->question,
                    'Réponse: ' . $reponse->reponse,
                    $reponse->valeur_numerique ? 'Note: ' . $reponse->valeur_numerique : '',
                    '',
                    '',
                ]);
            }

            // Ligne vide entre chaque évaluation
            $data->push(['', '', '', '', '', '']);
        }

        return $data;
    }

    public function headings(): array
    {
        return [
            'ID Évaluation',
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
        return 'Réponses Évaluations';
    }
}
