
<?php

namespace App\Exports;

use App\Models\Structure;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StructuresExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Structure::with(['typeStructure', 'ville'])->get();
    }

    public function headings(): array
    {
        return [
            'Nom',
            'Type',
            'Ville',
            'Adresse',
            'Téléphone',
            'Latitude',
            'Longitude',
            'Description',
            'Offre de services',
            'Statut',
            'Date de création',
        ];
    }

    public function map($structure): array
    {
        return [
            $structure->name,
            $structure->typeStructure?->name ?? 'N/A',
            $structure->ville?->name ?? 'N/A',
            $structure->adresse,
            $structure->phone,
            $structure->latitude,
            $structure->longitude,
            $structure->description ?? '',
            $structure->offre ?? '',
            $structure->status ? 'Actif' : 'Inactif',
            $structure->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
