<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class MediaFileApiController extends Controller
{
    /**
     * Search and list uploaded images.
     */
    public function search(Request $request)
    {
        // 1. Security Check
        Gate::authorize('access-admin-api');

        $queryParam = $request->input('q', '');
        $limit = (int) $request->input('limit', 24);
        if ($limit <= 0 || $limit > 100) {
            $limit = 24;
        }

        // We only want images (not PDFs/documents) for this picker
        $queryBuilder = MediaFile::query()
            ->where('file_type', 'like', 'image/%');

        if ($queryParam !== '') {
            $queryBuilder->where(function ($sub) use ($queryParam) {
                $sub->where('name', 'like', "%{$queryParam}%")
                    ->orWhere('file_path', 'like', "%{$queryParam}%");
            });
        }

        // Sort by created_at desc and id desc to show newest first
        $queryBuilder->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        // Paginate the query
        $paginator = $queryBuilder->paginate($limit);

        // Map data to return absolute URLs and necessary fields
        $data = collect($paginator->items())->map(function ($file) {
            return [
                'id' => $file->id,
                'name' => $file->name,
                'file_path' => $file->file_path,
                'url' => $file->url,
            ];
        });

        return response()->json([
            'data' => $data,
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'has_more' => $paginator->hasMorePages(),
        ]);
    }
}
