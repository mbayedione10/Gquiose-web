<?php

namespace App\Exports;

use App\Models\Utilisateur;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UtilisateursExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    public function collection()
    {
        return Utilisateur::with('ville')->get();
    }

    public function headings(): array
    {
        return [
            'Nom',
            'Prénom',
            'Email',
            'Téléphone',
            'Sexe',
            'Année de naissance',
            'Ville',
            'Statut',
            'Plateforme',
            'Date d\'inscription',
        ];
    }

    public function map($utilisateur): array
    {
        return [
            $utilisateur->nom,
            $utilisateur->prenom,
            $utilisateur->email,
            $utilisateur->phone,
            $utilisateur->sexe,
            $utilisateur->anneedenaissance,
            $utilisateur->ville?->name ?? 'N/A',
            $utilisateur->status ? 'Actif' : 'Inactif',
            $utilisateur->platform ?? 'N/A',
            $utilisateur->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
