
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('utilisateurs', function (Blueprint $table) {
            // Add a virtual generated column that concatenates prenom and nom
            $table->string('name')->virtualAs('CONCAT(prenom, " ", nom)')->after('prenom');
        });
    }

    public function down()
    {
        Schema::table('utilisateurs', function (Blueprint $table) {
            $table->dropColumn('name');
        });
    }
};
