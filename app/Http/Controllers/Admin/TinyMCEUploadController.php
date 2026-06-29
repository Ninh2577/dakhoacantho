<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TinyMCEUploadController extends Controller
{
    /**
     * Handle the incoming image upload request from TinyMCE.
     */
    public function upload(Request $request)
    {
        // 1. Authorize - only logged in users with admin role
        if (! auth()->check() || auth()->user()->role !== 'admin') {
            return response()->json(['error' => 'Unauthorized access.'], 403);
        }

        $fileKey = 'file';
        if ($request->hasFile('image')) {
            $fileKey = 'image';
        } elseif ($request->hasFile('blob')) {
            $fileKey = 'blob';
        }

        // 2. Validate request manually to control the exact JSON response format for TinyMCE
        $validator = Validator::make($request->all(), [
            $fileKey => 'required|image|mimes:jpg,jpeg,png,webp,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        $file = $request->file($fileKey);
        if (! $file->isValid()) {
            return response()->json(['error' => 'Invalid file upload.'], 400);
        }

        // 3. Define directory path: uploads/articles/YYYY/MM
        $year = date('Y');
        $month = date('m');

        // Sanitize original filename and append timestamp
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::slug($originalName).'-'.time().'.'.$extension;

        // --- Compress image using GD library ---
        $filePath = $file->getRealPath();
        $mimeType = $file->getMimeType();
        $quality = 75;

        if (in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp'])) {
            try {
                switch ($mimeType) {
                    case 'image/jpeg':
                        $image = @imagecreatefromjpeg($filePath);
                        if ($image) {
                            imagejpeg($image, $filePath, $quality);
                            imagedestroy($image);
                        }
                        break;
                    case 'image/webp':
                        $image = @imagecreatefromwebp($filePath);
                        if ($image) {
                            imagewebp($image, $filePath, $quality);
                            imagedestroy($image);
                        }
                        break;
                    case 'image/png':
                        $image = @imagecreatefrompng($filePath);
                        if ($image) {
                            imagealphablending($image, false);
                            imagesavealpha($image, true);
                            imagepng($image, $filePath, 8); // PNG compression 8
                            imagedestroy($image);
                        }
                        break;
                }
            } catch (\Exception $e) {
                // Skip compression on error and keep original
            }
        }

        // Store file on the public disk
        $path = $file->storeAs("uploads/articles/{$year}/{$month}", $fileName, 'public');

        if (! $path) {
            return response()->json(['error' => 'Failed to store uploaded file.'], 500);
        }

        // --- Sync with Media Library (media_files table) ---
        try {
            clearstatcache(true, Storage::disk('public')->path($path));
            $fileSize = Storage::disk('public')->size($path);

            \App\Models\MediaFile::create([
                'name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_type' => $mimeType,
                'file_size' => $fileSize,
            ]);
        } catch (\Exception $e) {
            // Silence DB exceptions to not interrupt user's editor experience
        }

        // 4. Return correct JSON structure expected by TinyMCE
        $url = Storage::disk('public')->url($path);

        if (! str_starts_with($url, 'http://') && ! str_starts_with($url, 'https://')) {
            $url = rtrim(config('app.url'), '/').'/'.ltrim($url, '/');
        }

        // Force HTTPS if APP_URL is configured as HTTPS to avoid mixed content
        if (str_starts_with(config('app.url'), 'https://')) {
            $url = str_replace('http://', 'https://', $url);
        }

        return response()->json([
            'location' => $url,
        ]);
    }
}
