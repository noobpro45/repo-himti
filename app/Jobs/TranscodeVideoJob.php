<?php

namespace App\Jobs;

use App\Models\Media;
use App\Models\Pengaturan;
use FFMpeg\Format\Video\X264;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TranscodeVideoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 jam maks
    public $media;

    public function __construct(Media $media)
    {
        $this->media = $media;
    }

    public function handle(): void
    {
        $media = $this->media;
        $originalPath = $media->path_file;
        
        $preset = Pengaturan::ambil('ffmpeg_preset', 'medium');
        
        $format = new X264();
        // Set audio bitrate and video bitrate (contoh)
        $format->setAudioKiloBitrate(128);
        
        if ($preset == 'veryfast') {
            $format->setAdditionalParameters(['-preset', 'veryfast', '-crf', '28']);
        } else {
            $format->setAdditionalParameters(['-preset', 'medium', '-crf', '23']);
        }

        $newFilename = Str::uuid() . '.mp4';
        $newPath = 'transcoded/' . $newFilename;
        try {
            
            // Transcode Video
            FFMpeg::fromDisk('media_private')
                ->open($originalPath)
                ->export()
                ->toDisk('media_private')
                ->inFormat($format)
                ->save($newPath);

            // Ekstrak Frame Pertama Video sebagai Thumbnail
            $thumbnailName = Str::uuid() . '.jpg';
            $thumbnailPath = 'thumbnails/' . $thumbnailName;
            try {
                FFMpeg::fromDisk('media_private')
                    ->open($newPath)
                    ->getFrameFromSeconds(0)
                    ->export()
                    ->toDisk('media_private')
                    ->save($thumbnailPath);
            } catch (\Exception $thumbEx) {
                \Illuminate\Support\Facades\Log::warning('Gagal mengekstrak frame thumbnail video', ['error' => $thumbEx->getMessage()]);
                $thumbnailPath = null;
            }

            // Dapatkan Durasi Video
            $durasiFormatted = null;
            try {
                $durationInSeconds = FFMpeg::fromDisk('media_private')
                    ->open($newPath)
                    ->getDurationInSeconds();
                
                $hours = floor($durationInSeconds / 3600);
                $minutes = floor(($durationInSeconds % 3600) / 60);
                $secs = $durationInSeconds % 60;

                if ($hours > 0) {
                    $durasiFormatted = sprintf('%02d:%02d:%02d', $hours, $minutes, $secs);
                } else {
                    $durasiFormatted = sprintf('%02d:%02d', $minutes, $secs);
                }
            } catch (\Exception $durEx) {
                \Illuminate\Support\Facades\Log::warning('Gagal mengambil durasi video', ['error' => $durEx->getMessage()]);
            }

            // Update Media
            $finalPathAbs = Storage::disk('media_private')->path($newPath);
            $newSize = filesize($finalPathAbs);

            $media->update([
                'path_file' => $newPath,
                'path_thumbnail' => $thumbnailPath,
                'durasi' => $durasiFormatted,
                'ukuran_byte' => $newSize,
                'status_proses' => 'selesai',
            ]);

            // Hapus file raw asli agar menghemat ruang
            Storage::disk('media_private')->delete($originalPath);

        } catch (\Exception $e) {
            $media->update(['status_proses' => 'gagal']);
            throw $e;
        }
    }
}
