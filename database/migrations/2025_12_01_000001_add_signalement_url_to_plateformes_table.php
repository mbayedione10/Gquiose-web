
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plateformes', function (Blueprint $table) {
            $table->string('signalement_url', 500)->nullable()->after('description')
                ->comment('URL pour signaler du contenu sur la plateforme');
        });
    }

    public function down(): void
    {
        Schema::table('plateformes', function (Blueprint $table) {
            $table->dropColumn('signalement_url');
        });
    }
};
