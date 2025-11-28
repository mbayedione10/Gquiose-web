
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sous_types_violence_numerique', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->text('description')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // Ajouter la colonne de relation dans la table alertes
        Schema::table('alertes', function (Blueprint $table) {
            $table->foreignId('sous_type_violence_numerique_id')
                ->nullable()
                ->after('type_alerte_id')
                ->constrained('sous_types_violence_numerique')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('alertes', function (Blueprint $table) {
            $table->dropForeign(['sous_type_violence_numerique_id']);
            $table->dropColumn('sous_type_violence_numerique_id');
        });

        Schema::dropIfExists('sous_types_violence_numerique');
    }
};
