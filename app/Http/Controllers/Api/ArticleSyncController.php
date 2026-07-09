<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleSyncController extends Controller
{
    /**
     * API đồng bộ bài viết kèm các điều kiện lọc linh hoạt.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getArticlesForSync(Request $request)
    {
        // 1. Bảo mật: Xác thực Token từ Headers hoặc Query Parameter
        $secretToken = config('services.sync.token');
        $token = $request->header('X-Sync-Token') ?: $request->input('token');
        if (empty($secretToken) || $token !== $secretToken) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // 2. Khởi tạo Query bài viết cùng quan hệ Category và các cấp cha (để map phân cấp theo slug lên tới 3 cấp)
        $query = Article::query()->with('category.parent.parent.parent');

        // 3. Lọc theo trạng thái xuất bản (Mặc định lấy bài viết đã xuất bản)
        $status = $request->get('status', 'published');
        if ($status === 'published') {
            $query->where('is_published', true);
        } elseif ($status === 'draft') {
            $query->where('is_published', false);
        }

        // Lọc theo danh sách slug cụ thể (Ví dụ: ?slugs=slug1,slug2)
        if ($request->filled('slugs')) {
            $slugsList = is_array($request->slugs) ? $request->slugs : explode(',', $request->slugs);
            $query->whereIn('slug', array_map('trim', $slugsList));
        }

        // Lọc theo từ khóa tiêu đề (Ví dụ: ?search=từ-khóa)
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // 4. Lọc theo một ngày cụ thể (Ví dụ: ?date=2026-07-09)
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // 5. Lọc theo khoảng ngày tạo (Ví dụ: ?start_date=2026-01-01&end_date=2026-07-09)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        // 6. Lọc theo danh mục slug (Ví dụ: ?category_slug=benh-tri)
        if ($request->filled('category_slug')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category_slug);
            });
        }

        // 7. Lọc theo những bài viết cập nhật gần đây (Ví dụ: ?updated_since=2026-07-09 00:00:00)
        if ($request->filled('updated_since')) {
            $query->where('updated_at', '>=', $request->updated_since);
        }

        // 8. Lấy dữ liệu phân trang để đảm bảo hiệu năng
        $perPage = (int) $request->get('per_page', 50);
        $articles = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($articles, 200, [], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
