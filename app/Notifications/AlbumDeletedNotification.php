<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AlbumDeletedNotification extends Notification
{
    use Queueable;

    public $namaAlbum;
    public $alasan;
    public $namaSuperAdmin;

    public function __construct($namaAlbum, $alasan, $namaSuperAdmin)
    {
        $this->namaAlbum = $namaAlbum;
        $this->alasan = $alasan;
        $this->namaSuperAdmin = $namaSuperAdmin;
    }

    public function via($notifiable)
    {
        // Menyimpan notifikasi hanya ke database
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'judul' => 'Album Dihapus',
            'pesan' => "Album \"{$this->namaAlbum}\" telah dihapus paksa oleh Super Admin ({$this->namaSuperAdmin}).",
            'alasan' => $this->alasan,
            'waktu' => now()->toDateTimeString(),
        ];
    }
}
