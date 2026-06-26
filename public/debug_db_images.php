<?php
/**
 * debug_db_images.php
 * ============================================================
 * Kiểm tra đường dẫn ảnh trong DB và kiểm tra sự tồn tại trên disk.
 * ============================================================
 */

header('Content-Type: text/html; charset=utf-8');

try {
    require __DIR__.'/../vendor/autoload.php';
    $app = require_once __DIR__.'/../bootstrap/app.php';
    
    // Bootstrap console kernel để có access vào DB và config
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();
    
    // Lấy 20 bài viết mới nhất
    $articles = Illuminate\Support\Facades\DB::table('articles')
        ->select('id', 'title', 'thumbnail_image')
        ->orderBy('id', 'desc')
        ->limit(30)
        ->get();
        
    echo '<style>body{font-family:monospace;padding:16px;background:#111;color:#eee;}
    table{border-collapse:collapse;width:100%;margin-top:20px;} td,th{border:1px solid #444;padding:6px 10px;}
    th{background:#1e3a5f;} .ok{color:#4ade80;} .err{color:#f87171;} .warn{color:#fbbf24;}
    img{max-width:80px;max-height:50px;} code{background:#222;padding:2px 5px;color:#fbbf24;}
    </style>';
    
    echo '<h2>🔍 Kiểm tra đường dẫn ảnh bài viết (Production)</h2>';
    echo '<p>Thư mục Public Storage thực tế: <code>' . public_path('storage') . '</code></p>';
    
    echo '<table><tr><th>ID</th><th>Tiêu đề</th><th>thumbnail_image (DB)</th><th>URL ảnh</th><th>Đường dẫn thực tế</th><th>File tồn tại?</th><th>Ảnh</th></tr>';
    
    foreach ($articles as $a) {
        $img = $a->thumbnail_image;
        
        if ($img) {
            // Laravel default storage URL
            $url = asset('storage/' . $img);
            // Thực tế trên Server
            $path = public_path('storage/' . $img);
            $exists = file_exists($path);
            
            // Nếu không tồn tại, thử tìm xem nó nằm ở đâu khác không (ví dụ trong uploads)
            $alternative = '';
            if (!$exists) {
                // Thử tìm trong uploads/ hoặc vị trí khác
                $baseName = basename($img);
                // Tìm kiếm trong storage/app/public xem có file trùng tên không
                $searchDir = storage_path('app/public');
                $alternative = findFileRecursive($searchDir, $baseName);
                if ($alternative) {
                    $alternative = str_replace(storage_path('app/public/'), 'storage/', $alternative);
                }
            }
            
            echo '<tr>';
            echo '<td>' . $a->id . '</td>';
            echo '<td style="font-size:12px">' . htmlspecialchars(mb_substr($a->title ?? '', 0, 50)) . '</td>';
            echo '<td><code>' . htmlspecialchars($img) . '</code></td>';
            echo '<td><a href="' . $url . '" target="_blank" style="color:#60a5fa;font-size:11px">Xem Link</a></td>';
            echo '<td><span style="font-size:11px">' . htmlspecialchars($path) . '</span>' . ($alternative ? '<br><span class="warn">Tìm thấy ở: ' . htmlspecialchars($alternative) . '</span>' : '') . '</td>';
            echo '<td class="' . ($exists ? 'ok' : 'err') . '">' . ($exists ? '✅ Có' : '❌ Không') . '</td>';
            echo '<td>' . ($exists ? '<img src="' . $url . '">' : '—') . '</td>';
            echo '</tr>';
        } else {
            echo '<tr>';
            echo '<td>' . $a->id . '</td>';
            echo '<td style="font-size:12px">' . htmlspecialchars(mb_substr($a->title ?? '', 0, 50)) . '</td>';
            echo '<td colspan="5" class="warn" style="text-align:center;">— Không có thumbnail_image —</td>';
            echo '</tr>';
        }
    }
    echo '</table>';
    
} catch (Exception $e) {
    echo '<p class="err">Lỗi: ' . htmlspecialchars($e->getMessage()) . '</p>';
    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
}

function findFileRecursive($dir, $fileName) {
    if (!is_dir($dir)) return null;
    $items = array_diff(scandir($dir), ['.', '..']);
    foreach ($items as $item) {
        $path = $dir . '/' . $item;
        if (is_dir($path)) {
            $res = findFileRecursive($path, $fileName);
            if ($res) return $res;
        } else {
            if ($item === $fileName) {
                return $path;
            }
        }
    }
    return null;
}
