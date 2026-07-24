<?php

namespace Database\Seeders;

use App\Models\Album;
use App\Models\LogAktivitas;
use App\Models\Mahasiswa;
use App\Models\Pengaturan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. Super Admin ──
        $superAdmin = User::create([
            'username'      => 'super_admin',
            'password'      => 'password',   // will be bcrypt-ed by cast
            'nama_lengkap'  => 'Pengurus Inti',
            'role'          => 'super_admin',
        ]);

        // ── 2. Admin PDD ──
        $adminPdd = User::create([
            'username'      => 'admin_pdd',
            'password'      => 'password',
            'nama_lengkap'  => 'Delegasi Media Kreatif',
            'role'          => 'admin_pdd',
        ]);

        // ── 3. Sample Mahasiswa ──
        $mhsFarah = User::create([
            'username'      => '11230610001',
            'password'      => 'password',
            'nama_lengkap'  => 'Farah Maulida',
            'role'          => 'mahasiswa',
        ]);
        Mahasiswa::create([
            'id_user'       => $mhsFarah->id_user,
            'nim'           => '11230610001',
            'program_studi' => 'Teknik Informatika',
            'angkatan'      => 2023,
        ]);

        $mhsBagas = User::create([
            'username'      => '11230610002',
            'password'      => 'password',
            'nama_lengkap'  => 'Bagas Pratama',
            'role'          => 'mahasiswa',
        ]);
        Mahasiswa::create([
            'id_user'       => $mhsBagas->id_user,
            'nim'           => '11230610002',
            'program_studi' => 'Teknik Informatika',
            'angkatan'      => 2023,
        ]);

        $mhsAisyah = User::create([
            'username'      => '11230610003',
            'password'      => 'password',
            'nama_lengkap'  => 'Aisyah Rahmawati',
            'role'          => 'admin_pdd',
        ]);

        // ── 4. Sample Albums ──
        $albums = [
            [
                'id_user'               => $adminPdd->id_user,
                'nama_acara'            => 'Seminar Nasional Teknologi Informasi 2026',
                'tanggal_acara'         => '2026-07-12',
                'tanggal_acara_selesai' => '2026-07-14',
                'deskripsi'             => 'Dokumentasi rangkaian seminar nasional bertema kecerdasan buatan dan keamanan siber, diselenggarakan oleh Departemen Media Kreatif HIMTI.',
                'cover_gradient'        => '#1B2D6B,#0F172A',
            ],
            [
                'id_user'        => $adminPdd->id_user,
                'nama_acara'     => 'Coding Camp Batch 6',
                'tanggal_acara'  => '2026-06-02',
                'deskripsi'      => 'Pelatihan pemrograman intensif batch keenam untuk anggota baru HIMTI.',
                'cover_gradient' => '#2EB253,#1E3A24',
            ],
            [
                'id_user'        => $mhsAisyah->id_user,
                'nama_acara'     => 'Malam Keakraban Maba',
                'tanggal_acara'  => '2025-09-20',
                'deskripsi'      => 'Acara keakraban mahasiswa baru program studi Teknik Informatika.',
                'cover_gradient' => '#2E8B57,#153F2A',
            ],
            [
                'id_user'        => $adminPdd->id_user,
                'nama_acara'     => 'Rapat Kerja Nasional',
                'tanggal_acara'  => '2025-03-14',
                'deskripsi'      => 'Dokumentasi rapat kerja nasional pengurus HIMTI.',
                'cover_gradient' => '#B9503F,#4E2119',
            ],
            [
                'id_user'        => $mhsAisyah->id_user,
                'nama_acara'     => 'Lomba Hackathon Kampus',
                'tanggal_acara'  => '2024-11-09',
                'deskripsi'      => 'Kompetisi hackathon antar-kampus bertema Smart City.',
                'cover_gradient' => '#4B5F73,#1D2733',
            ],
            [
                'id_user'        => $adminPdd->id_user,
                'nama_acara'     => 'Pelantikan Pengurus 2024',
                'tanggal_acara'  => '2024-02-03',
                'deskripsi'      => 'Upacara pelantikan kepengurusan HIMTI periode 2024.',
                'cover_gradient' => '#7A4FA0,#2E1E3D',
            ],
        ];

        foreach ($albums as $data) {
            Album::create($data);
        }

        // ── 5. Sample Log Aktivitas ──
        LogAktivitas::catat($mhsAisyah->id_user, 'Mengunggah 24 media ke Seminar Nasional TI', '10.20.4.11');
        LogAktivitas::catat($mhsBagas->id_user, 'Membuat album Coding Camp Batch 6', '10.20.4.18');
        LogAktivitas::catat($superAdmin->id_user, 'Mengubah kata sandi anggota 1123061004', '10.20.1.2');
        LogAktivitas::catat($mhsBagas->id_user, 'Menghapus album Rapat Kerja 2025 (draft)', '10.20.4.18');

        // ── 6. Pengaturan Default ──
        Pengaturan::setel('max_upload_size_mb', '500');
        Pengaturan::setel('chunk_size_mb', '10');
        Pengaturan::setel('allowed_mime_types', 'image/*,video/*');
        Pengaturan::setel('ffmpeg_preset', 'medium');
        Pengaturan::setel('nama_organisasi', 'HIMTI UIN Syarif Hidayatullah Jakarta');
        Pengaturan::setel('storage_path', '/storage/himti');
        Pengaturan::setel('wa_admin', '6281234567890');
    }
}
