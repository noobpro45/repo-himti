<?php

namespace App\Http\Controllers;

use App\Models\Album;
use App\Models\Media;
use App\Models\Pengaturan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class UploadController extends Controller
{
    /**
     * Membuat thumbnail untuk foto menggunakan GD.
     */
    private function generateImageThumbnail($sourcePath, $thumbnailPath, $mimeType, $maxWidth = 600, $maxHeight = 600)
    {
        if (!extension_loaded('gd')) return false;

        $absSource = Storage::disk('media_private')->path($sourcePath);
        $absDest = Storage::disk('media_private')->path($thumbnailPath);

        // Pastikan direktori ada
        $destDir = dirname($absDest);
        if (!is_dir($destDir)) {
            mkdir($destDir, 0755, true);
        }

        switch ($mimeType) {
            case 'image/jpeg':
                $image = @imagecreatefromjpeg($absSource);
                if ($image && function_exists('exif_read_data')) {
                    $exif = @exif_read_data($absSource);
                    if ($exif && isset($exif['Orientation'])) {
                        switch ($exif['Orientation']) {
                            case 3:
                                $rotated = imagerotate($image, 180, 0);
                                if ($rotated !== false) { imagedestroy($image); $image = $rotated; }
                                break;
                            case 6:
                                $rotated = imagerotate($image, -90, 0);
                                if ($rotated !== false) { imagedestroy($image); $image = $rotated; }
                                break;
                            case 8:
                                $rotated = imagerotate($image, 90, 0);
                                if ($rotated !== false) { imagedestroy($image); $image = $rotated; }
                                break;
                        }
                    }
                }
                break;
            case 'image/png':
                $image = @imagecreatefrompng($absSource);
                break;
            case 'image/webp':
                $image = @imagecreatefromwebp($absSource);
                break;
            default:
                return false;
        }

        if (!$image) return false;

        $width = imagesx($image);
        $height = imagesy($image);

        $ratio = min($maxWidth / $width, $maxHeight / $height);
        if ($ratio >= 1) {
            $newWidth = $width;
            $newHeight = $height;
        } else {
            $newWidth = intval($width * $ratio);
            $newHeight = intval($height * $ratio);
        }

        $thumbnail = imagecreatetruecolor($newWidth, $newHeight);
        
        if ($mimeType == 'image/png' || $mimeType == 'image/webp') {
            imagealphablending($thumbnail, false);
            imagesavealpha($thumbnail, true);
            $transparent = imagecolorallocatealpha($thumbnail, 255, 255, 255, 127);
            imagefilledrectangle($thumbnail, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($thumbnail, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        
        $success = imagejpeg($thumbnail, $absDest, 85);
        
        imagedestroy($image);
        imagedestroy($thumbnail);

        return $success;
    }
    public function uploadChunk(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'album_id' => 'required|exists:tb_album,id_album',
            'file' => 'required|file',
            'dzuuid' => 'required|string',
            'dzchunkindex' => 'required|integer',
            'dztotalchunkcount' => 'required|integer',
            'dzchunksize' => 'required|integer',
            'dztotalfilesize' => 'required|integer',
        ]);

        $albumId = $validated['album_id'];
        $file = $request->file('file');
        $uuid = $validated['dzuuid'];
        $chunkIndex = $validated['dzchunkindex'];
        $totalChunks = $validated['dztotalchunkcount'];
        $fileName = $file->getClientOriginalName();

        // Cek ukuran total
        $maxUploadSizeMB = (int) Pengaturan::ambil('max_upload_size_mb', 500);
        $maxUploadSizeBytes = $maxUploadSizeMB * 1024 * 1024;
        
        if ($validated['dztotalfilesize'] > $maxUploadSizeBytes) {
            return response()->json(['error' => 'File size exceeds maximum limit of ' . $maxUploadSizeMB . ' MB'], 400);
        }

        // Folder untuk menyimpan chunk sementara per UUID file
        $chunkPath = "temp/{$uuid}";
        
        // Simpan chunk
        Storage::disk('chunks')->putFileAs($chunkPath, $file, "{$chunkIndex}.part");

        // Return success for this chunk (Merging is now handled by a separate API call)
        return response()->json(['success' => true, 'chunkIndex' => $chunkIndex]);
    }

    public function uploadStatus(Request $request)
    {
        $uuid = $request->query('dzuuid');
        if (!$uuid) {
            return response()->json(['uploaded_chunks' => []]);
        }
        $chunkPath = "temp/{$uuid}";
        $filesInChunkDir = Storage::disk('chunks')->files($chunkPath);
        
        $uploaded = array_map(function($path) {
            return (int) pathinfo($path, PATHINFO_FILENAME);
        }, $filesInChunkDir);

        return response()->json(['uploaded_chunks' => array_values($uploaded)]);
    }

    public function checkHash(Request $request)
    {
        $validated = $request->request->all(); // Fallback if validate fails on empty
        $albumId = $request->input('album_id');
        $fileHash = $request->input('file_hash');
        $fileName = $request->input('file_name');

        if (!$albumId || !$fileHash || !$fileName) {
            return response()->json(['exists' => false]);
        }

        $existingMedia = Media::where('file_hash', $fileHash)->first();
        if ($existingMedia) {
            $album = Album::find($albumId);
            if ($existingMedia->id_album == $albumId) {
                return response()->json([
                    'exists' => true,
                    'media_id' => $existingMedia->id_media,
                    'status' => $existingMedia->status_proses,
                    'html' => view('album.partials.media_tile', ['item' => $existingMedia, 'album' => $album])->render()
                ]);
            }

            $media = Media::create([
                'id_album' => $albumId,
                'id_user' => Auth::id(),
                'nama_file_asli' => $fileName,
                'nama_file_server' => $existingMedia->nama_file_server,
                'path_file' => $existingMedia->path_file,
                'path_thumbnail' => $existingMedia->path_thumbnail,
                'mime_type' => $existingMedia->mime_type,
                'tipe' => $existingMedia->tipe,
                'ukuran_byte' => $existingMedia->ukuran_byte,
                'width' => $existingMedia->width,
                'height' => $existingMedia->height,
                'durasi' => $existingMedia->durasi,
                'status_proses' => $existingMedia->status_proses,
                'catatan_proses' => null,
                'file_hash' => $fileHash,
            ]);

            return response()->json([
                'exists' => true,
                'media_id' => $media->id_media,
                'status' => $media->status_proses,
                'html' => view('album.partials.media_tile', ['item' => $media, 'album' => $album])->render()
            ]);
        }

        $uploadedChunks = [];
        $uuid = $request->input('dzuuid');
        if ($uuid) {
            $chunkPath = "temp/{$uuid}";
            if (Storage::disk('chunks')->exists($chunkPath)) {
                $files = Storage::disk('chunks')->files($chunkPath);
                foreach ($files as $file) {
                    if (preg_match('/(\d+)\.part$/', $file, $matches)) {
                        $uploadedChunks[] = (int) $matches[1];
                    }
                }
            }
        }

        return response()->json([
            'exists' => false,
            'uploaded_chunks' => $uploadedChunks
        ]);
    }

    public function mergeChunksApi(Request $request)
    {
        $validated = $request->validate([
            'album_id' => 'required|exists:tb_album,id_album',
            'dzuuid' => 'required|string',
            'dztotalchunkcount' => 'required|integer',
            'dztotalfilesize' => 'required|integer',
            'file_name' => 'required|string',
            'file_hash' => 'nullable|string',
        ]);

        $uuid = $validated['dzuuid'];
        $totalChunks = $validated['dztotalchunkcount'];
        $fileName = $validated['file_name'];
        $albumId = $validated['album_id'];
        $totalSize = $validated['dztotalfilesize'];

        $fileHash = $request->input('file_hash');

        // Cek apakah semua chunk tersedia
        $chunkPath = "temp/{$uuid}";
        $filesInChunkDir = Storage::disk('chunks')->files($chunkPath);
        if (count($filesInChunkDir) != $totalChunks) {
            return response()->json(['error' => 'Missing chunks. Only found ' . count($filesInChunkDir) . ' out of ' . $totalChunks], 400);
        }

        return $this->mergeChunks($uuid, $totalChunks, $fileName, $albumId, $totalSize, $fileHash);
    }

    private function mergeChunks($uuid, $totalChunks, $fileName, $albumId, $totalSize, $fileHash = null)
    {
        $extension = pathinfo($fileName, PATHINFO_EXTENSION);
        $finalFilename = Str::uuid()->toString() . '.' . $extension;
        $finalPath = 'raw/' . $finalFilename;
        $chunkPath = "temp/{$uuid}";

        // Buka file tujuan di disk media_private
        Storage::disk('media_private')->put($finalPath, '');
        
        // Mode append ke file tujuan
        $finalFileAbsolutePath = Storage::disk('media_private')->path($finalPath);
        $finalFile = fopen($finalFileAbsolutePath, 'a');

        for ($i = 0; $i < $totalChunks; $i++) {
            $chunkFileAbsolutePath = Storage::disk('chunks')->path("{$chunkPath}/{$i}.part");
            
            $chunkFile = fopen($chunkFileAbsolutePath, 'rb');
            stream_copy_to_stream($chunkFile, $finalFile);
            fclose($chunkFile);
        }

        fclose($finalFile);

        // Hapus chunk setelah digabung
        Storage::disk('chunks')->deleteDirectory($chunkPath);

        $mimeType = mime_content_type($finalFileAbsolutePath);
        if (!$mimeType) {
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = $fileInfo ? finfo_file($fileInfo, $finalFileAbsolutePath) : false;
            if ($fileInfo) {
                finfo_close($fileInfo);
            }
        }
        $mimeType = $mimeType ?: 'application/octet-stream';
        
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $validImageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'heic', 'heif', 'raw', 'cr2', 'cr3', 'nef', 'arw', 'dng', 'raf', 'orf', 'sr2', 'pef', 'rw2'];
        $validVideoExtensions = ['mp4', 'mov', 'avi', 'mkv', 'webm', 'm4v', 'hevc'];

        // Blokir manipulasi SVG XSS
        $isImage = (str_starts_with($mimeType, 'image/') && !str_contains($mimeType, 'svg')) || (in_array($extension, $validImageExtensions) && $extension !== 'svg');
        $isVideo = str_starts_with($mimeType, 'video/') || in_array($extension, $validVideoExtensions);

        if (!$isImage && (!$isVideo)) {
            Storage::disk('media_private')->delete($finalPath);

            return response()->json([
                'error' => 'File type is not supported. Please upload an image or video.'
            ], 422);
        }

        $tipe = $isVideo ? 'video' : 'foto';
        $statusProses = $isVideo ? 'diproses' : 'selesai';

        // Dapatkan resolusi untuk gambar (durasi video ditangani oleh Background Job)
        $width = null;
        $height = null;
        if ($isImage && $info = @getimagesize($finalFileAbsolutePath)) {
            $width = $info[0];
            $height = $info[1];
        }

        try {
            $media = Media::create([
                'id_album' => $albumId,
                'id_user' => Auth::id(),
                'nama_file_asli' => $fileName,
                'nama_file_server' => $finalFilename,
                'path_file' => $finalPath,
                'path_thumbnail' => null,
                'mime_type' => $mimeType,
                'tipe' => $tipe,
                'ukuran_byte' => $totalSize,
                'width' => $width,
                'height' => $height,
                'durasi' => null,
                'status_proses' => $statusProses,
                'catatan_proses' => null,
                'file_hash' => $fileHash,
            ]);
        } catch (\Throwable $e) {
            Log::error('Gagal menyimpan media hasil upload chunk', [
                'album_id' => $albumId,
                'file_name' => $fileName,
                'mime_type' => $mimeType,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }

        // --- Pembuatan Thumbnail Sinkron ---
        $thumbnailPath = null;
        try {
            $thumbnailName = Str::uuid() . '.jpg';
            $tempThumbPath = 'thumbnails/' . $thumbnailName;

            if ($isVideo) {
                // Ekstrak Frame Pertama Video dipindahkan ke TranscodeVideoJob (Background) untuk mencegah bottleneck
                $thumbnailPath = null;
            } else if ($isImage) {
                // Resize Foto
                $success = $this->generateImageThumbnail($finalPath, $tempThumbPath, $mimeType);
                if ($success) {
                    $thumbnailPath = $tempThumbPath;
                }
            }

            if ($thumbnailPath) {
                $media->update(['path_thumbnail' => $thumbnailPath]);
            }
        } catch (\Exception $e) {
            Log::warning('Gagal membuat thumbnail sinkron', ['error' => $e->getMessage()]);
            // Tidak perlu abort, biarkan upload tetap sukses
        }
        // -----------------------------------

        if ($isVideo) {
            \App\Jobs\TranscodeVideoJob::dispatch($media);
        }

        $album = Album::find($albumId);

        return response()->json([
            'success' => true, 
            'media_id' => $media->id_media,
            'status' => $media->status_proses,
            'html' => view('album.partials.media_tile', ['item' => $media, 'album' => $album])->render()
        ]);
    }
}
