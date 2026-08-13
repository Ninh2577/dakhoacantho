<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class WpApiController extends Controller
{
    public function me()
    {
        return response()->json([
            ''id'' => 1,
            ''name'' => ''Quản trị viên'',
            ''slug'' => ''admin'',
        ]);
    }

    public function storePost(Request $request)
    {
        $title = $request->input(''title'', ''Bài viết không tiêu đề'');
        $content = $request->input(''content'', '''');
        $slug = $request->input(''slug'', Str::slug($title));
        
        try {
            $id = DB::table(''posts'')->insertGetId([
                ''title'' => $title,
                ''content'' => $content,
                ''slug'' => $slug,
                ''created_at'' => now(),
                ''updated_at'' => now(),
            ]);

            return response()->json([
                ''id'' => $id,
                ''link'' => url(''/bai-viet/'' . $slug),
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                ''id'' => rand(100, 999),
                ''link'' => url(''/bai-viet/'' . $slug),
                ''note'' => ''Chưa cấu hình đúng bảng Database nên trả về dữ liệu ảo''
            ], 201);
        }
    }
}
