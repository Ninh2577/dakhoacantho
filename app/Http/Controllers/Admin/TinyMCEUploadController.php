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
        if (!auth()->check() || auth()->user()->role !== 'admin') {
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
        if (!$file->isValid()) {
            return response()->json(['error' => 'Invalid file upload.'], 400);
        }

        // 3. Define directory path: uploads/articles/YYYY/MM
        $year = date('Y');
        $month = date('m');
        
        // Sanitize original filename and append timestamp
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::slug($originalName) . '-' . time() . '.' . $extension;

        // Store file on the public disk
        $path = $file->storeAs("uploads/articles/{$year}/{$month}", $fileName, 'public');

        if (!$path) {
            return response()->json(['error' => 'Failed to store uploaded file.'], 500);
        }

        // 4. Return correct JSON structure expected by TinyMCE
        $url = Storage::disk('public')->url($path);

        if (!str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
            $url = rtrim(config('app.url'), '/') . '/' . ltrim($url, '/');
        }

        // Force HTTPS if APP_URL is configured as HTTPS to avoid mixed content
        if (str_starts_with(config('app.url'), 'https://')) {
            $url = str_replace('http://', 'https://', $url);
        }

        return response()->json([
            'location' => $url
        ]);
    }
}
