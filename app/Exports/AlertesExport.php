<?php

namespace App\Exports;

use App\Models\Alerte;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AlertesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Alerte::with(['utilisateur', 'typeAlerte', 'ville'])->get();
    }

    public function headings(): array
    {
        return [
            'Référence',
            'Type',
            'Description',
            'État',
            'Ville',
            'Signalée par',
            'Latitude',
            'Longitude',
            'Date de création',
        ];
    }

    public function map($alerte): array
    {
        return [
            $alerte->ref,
            $alerte->typeAlerte?->name ?? 'N/A',
            $alerte->description,
            $alerte->etat,
            $alerte->ville?->name ?? 'N/A',
            $alerte->utilisateur?->nom.' '.$alerte->utilisateur?->prenom ?? 'N/A',
            $alerte->latitude,
            $alerte->longitude,
            $alerte->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
