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
            $table->index('tanggal_acara');
        });

        Schema::table('tb_media', function (Blueprint $table) {
            $table->index('tipe');
            $table->index('status_proses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_album', function (Blueprint $table) {
            $table->dropIndex(['tanggal_acara']);
        });

        Schema::table('tb_media', function (Blueprint $table) {
            $table->dropIndex(['tipe']);
            $table->dropIndex(['status_proses']);
        });
    }
};
