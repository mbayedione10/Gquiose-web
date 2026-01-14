
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alertes', function (Blueprint $table) {
            // Champs pour l'anonymisation de localisation
            $table->string('precision_localisation')->default('approximative')->after('longitude')
                ->comment('Précision de la localisation: exacte ou approximative');

            $table->float('rayon_approximation_km')->nullable()->after('precision_localisation')
                ->comment('Rayon d\'approximation appliqué en km');

            $table->string('quartier')->nullable()->after('rayon_approximation_km')
                ->comment('Quartier détecté');

            $table->string('commune')->nullable()->after('quartier')
                ->comment('Commune détectée');
        });
    }

    public function down(): void
    {
        Schema::table('alertes', function (Blueprint $table) {
            $table->dropColumn([
                'precision_localisation',
                'rayon_approximation_km',
                'quartier',
                'commune',
            ]);
        });
    }
};
