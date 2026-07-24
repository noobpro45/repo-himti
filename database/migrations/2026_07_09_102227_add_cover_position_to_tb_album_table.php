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
            $table->string('cover_position')->nullable()->default('center center')->after('id_media_cover');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_album', function (Blueprint $table) {
            $table->dropColumn('cover_position');
        });
    }
};
