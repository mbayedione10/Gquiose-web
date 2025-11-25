
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('utilisateurs', function (Blueprint $table) {
            $table->string('provider')->nullable()->after('password');
            $table->string('provider_id')->nullable()->after('provider');
            $table->string('photo')->nullable()->after('provider_id');
            $table->timestamp('email_verified_at')->nullable()->after('status');
            
            $table->index(['provider', 'provider_id']);
        });

        Schema::table('codes', function (Blueprint $table) {
            if (!Schema::hasColumn('codes', 'phone')) {
                $table->string('phone')->nullable()->after('email');
            }
        });
    }

    public function down(): void
    {
        Schema::table('utilisateurs', function (Blueprint $table) {
            $table->dropColumn(['provider', 'provider_id', 'photo', 'email_verified_at']);
        });

        Schema::table('codes', function (Blueprint $table) {
            if (Schema::hasColumn('codes', 'phone')) {
                $table->dropColumn('phone');
            }
        });
    }
};
