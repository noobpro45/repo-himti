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
        Schema::table('tb_album', function (Blueprint $table) {
            $table->uuid('id_media_cover')->nullable()->after('cover_gradient');
            
            $table->foreign('id_media_cover')
                  ->references('id_media')
                  ->on('tb_media')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_album', function (Blueprint $table) {
            $table->dropForeign(['id_media_cover']);
            $table->dropColumn('id_media_cover');
        });
    }
};
