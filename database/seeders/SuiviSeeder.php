<?php

namespace Database\Seeders;

use App\Models\Alerte;
use App\Models\Suivi;
use Illuminate\Database\Seeder;

class SuiviSeeder extends Seeder
{
    public function run(): void
    {
        $alertes = Alerte::limit(5)->get();

        if ($alertes->isEmpty()) {
            $this->command->warn('⚠️  Aucune alerte trouvée. Exécutez d\'abord AlerteSeeder.');

            return;
        }

        $suivis = [];

        foreach ($alertes as $index => $alerte) {
            $suivis[] = [
                'name' => 'Suivi initial - '.$alerte->ref,
                'observation' => 'Alerte créée et diffusée. En attente de retours.',
                'alerte_id' => $alerte->id,
            ];

            $suivis[] = [
                'name' => 'Point intermédiaire - '.$alerte->ref,
                'observation' => 'Bonne réception de l\'alerte. Actions en cours sur le terrain.',
                'alerte_id' => $alerte->id,
            ];
        }

        foreach ($suivis as $suivi) {
            Suivi::firstOrCreate(
                [
                    'name' => $suivi['name'],
                    'alerte_id' => $suivi['alerte_id'],
                ],
                $suivi
            );
        }

        $this->command->info('✅ '.count($suivis).' suivis d\'alertes créés');
    }
}
