
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('information', function (Blueprint $table) {
            $table->string('numero_cybercriminalite')->nullable()->after('structure_url');
            $table->string('email_cybercriminalite')->nullable()->after('numero_cybercriminalite');
        });
    }

    public function down(): void
    {
        Schema::table('information', function (Blueprint $table) {
            $table->dropColumn(['numero_cybercriminalite', 'email_cybercriminalite']);
        });
    }
};
