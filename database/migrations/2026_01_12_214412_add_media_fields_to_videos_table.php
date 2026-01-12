<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->enum('type', ['youtube', 'file'])->default('youtube')->after('url');
            $table->string('video_file')->nullable()->after('type');
            $table->string('thumbnail')->nullable()->after('video_file');
            $table->string('subtitle_file')->nullable()->after('thumbnail');
            $table->string('audiodescription_file')->nullable()->after('subtitle_file');
            $table->string('duration')->nullable()->after('audiodescription_file');
            $table->string('resolution')->nullable()->after('duration');
            $table->unsignedBigInteger('file_size')->nullable()->after('resolution');
            $table->boolean('status')->default(true)->after('file_size');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn([
                'description',
                'type',
                'video_file',
                'thumbnail',
                'subtitle_file',
                'audiodescription_file',
                'duration',
                'resolution',
                'file_size',
                'status',
            ]);
        });
    }
};
