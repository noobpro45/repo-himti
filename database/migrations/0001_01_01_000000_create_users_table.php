<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop default Laravel users table and rebuild with our schema
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');

        // ── tb_users ──────────────────────────────────────────
        Schema::create('tb_users', function (Blueprint $table) {
            $table->uuid('id_user')->primary();
            $table->string('username', 50)->unique();          // NIM atau kode admin
            $table->string('password');                         // bcrypt
            $table->string('nama_lengkap', 150);
            $table->enum('role', ['super_admin', 'admin_pdd', 'mahasiswa']);
            $table->boolean('is_aktif')->default(true);
            $table->timestamps();
        });

        // ── tb_mahasiswa (one-to-one ke tb_users) ─────────────
        Schema::create('tb_mahasiswa', function (Blueprint $table) {
            $table->uuid('id_mahasiswa')->primary();
            $table->foreignUuid('id_user')
                  ->constrained('tb_users', 'id_user')
                  ->cascadeOnDelete();
            $table->string('nim', 20)->unique();
            $table->string('program_studi', 100)->nullable();
            $table->year('angkatan')->nullable();
            $table->timestamps();
        });

        // ── tb_album ─────────────────────────────────────────
        Schema::create('tb_album', function (Blueprint $table) {
            $table->uuid('id_album')->primary();
            $table->foreignUuid('id_user')
                  ->constrained('tb_users', 'id_user')
                  ->cascadeOnDelete();
            $table->string('nama_acara', 200);
            $table->string('slug', 220)->unique();
            $table->date('tanggal_acara');
            $table->date('tanggal_acara_selesai')->nullable();  // range period
            $table->text('deskripsi')->nullable();
            $table->string('cover_gradient', 50)->nullable();   // e.g. "#1B2D6B,#0F172A"
            $table->timestamps();
        });

        // ── tb_media ─────────────────────────────────────────
        Schema::create('tb_media', function (Blueprint $table) {
            $table->uuid('id_media')->primary();
            $table->foreignUuid('id_album')
                  ->constrained('tb_album', 'id_album')
                  ->cascadeOnDelete();
            $table->foreignUuid('id_user')
                  ->constrained('tb_users', 'id_user')
                  ->cascadeOnDelete();
            $table->string('nama_file_asli', 255);
            $table->string('nama_file_server', 255)->unique();  // hashed/renamed
            $table->string('path_file', 500);
            $table->string('path_thumbnail', 500)->nullable();
            $table->string('mime_type', 80);
            $table->enum('tipe', ['foto', 'video']);
            $table->unsignedBigInteger('ukuran_byte');           // file size in bytes
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->string('durasi', 15)->nullable();            // e.g. "12:04"
            $table->enum('status_proses', ['selesai', 'mengunggah', 'diproses', 'ditolak', 'gagal'])
                  ->default('mengunggah');
            $table->text('catatan_proses')->nullable();          // e.g. "MIME-Type bukan image/*"
            $table->timestamps();
        });

        // ── tb_log_aktivitas ─────────────────────────────────
        Schema::create('tb_log_aktivitas', function (Blueprint $table) {
            $table->uuid('id_log')->primary();
            $table->foreignUuid('id_user')
                  ->constrained('tb_users', 'id_user')
                  ->cascadeOnDelete();
            $table->string('aktivitas', 500);
            $table->string('alamat_ip', 45)->nullable();
            $table->text('detail')->nullable();                  // JSON or text context
            $table->timestamps();
        });

        // ── tb_pengaturan ────────────────────────────────────
        Schema::create('tb_pengaturan', function (Blueprint $table) {
            $table->id();                                        // auto-increment (no UUID)
            $table->string('kunci', 100)->unique();
            $table->text('nilai')->nullable();
            $table->string('deskripsi', 255)->nullable();
            $table->timestamps();
        });

        // ── sessions (Laravel session driver: database) ──────
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignUuid('user_id')
                  ->nullable()
                  ->constrained('tb_users', 'id_user')
                  ->nullOnDelete();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('tb_pengaturan');
        Schema::dropIfExists('tb_log_aktivitas');
        Schema::dropIfExists('tb_media');
        Schema::dropIfExists('tb_album');
        Schema::dropIfExists('tb_mahasiswa');
        Schema::dropIfExists('tb_users');
    }
};
