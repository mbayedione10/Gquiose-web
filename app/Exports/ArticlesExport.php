<?php

namespace App\Exports;

use App\Models\Article;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ArticlesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Article::with(['rubrique', 'user'])->get();
    }

    public function headings(): array
    {
        return [
            'Titre',
            'Rubrique',
            'Auteur',
            'Statut',
            'Vedette',
            'Vues',
            'Date de publication',
        ];
    }

    public function map($article): array
    {
        return [
            $article->title,
            $article->rubrique?->name ?? 'N/A',
            $article->user?->name ?? 'N/A',
            $article->status ? 'Publié' : 'Brouillon',
            $article->vedette ? 'Oui' : 'Non',
            $article->views ?? 0,
            $article->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
