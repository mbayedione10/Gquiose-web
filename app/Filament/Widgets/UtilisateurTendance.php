<?php

namespace App\Filament\Widgets;
use App\Models\Utilisateur;
use Filament\Widgets\LineChartWidget;
use Illuminate\Support\Facades\DB;
class UtilisateurTendance extends LineChartWidget
{
    protected static ?string $heading = 'Évolution des utilisateurs';
    protected int | string | array $columnSpan = "full";
    protected static ?int $sort = 10;
    protected function getData(): array
    {
        $query = Utilisateur::select(DB::raw("COUNT(*) as data"), DB::raw("DATE_FORMAT(created_at, '%M') as label"))
            ->groupBy('label')
            ->orderByRaw("FIELD(label,'January','February','March',  'April', 'May', 'June','July','August','September','October','November','December')");
        $labels = $query->pluck('label')->toArray();
        $data = $query->pluck('data')->toArray();
        return [
            'datasets' => [
                [
                    'label' => 'Nouvel utilisateur',
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }
}
