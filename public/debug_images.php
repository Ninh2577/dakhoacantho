<?php
/**
 * debug_images.php — Debug đường dẫn ảnh
 * http://localhost/dakhoacantho_web/public/debug_images.php
 */
header('Content-Type: text/html; charset=utf-8');
echo '<!DOCTYPE html><html><head><meta charset="UTF-8"><title>Debug Images</title>
<style>body{font-family:monospace;padding:20px;background:#111;color:#eee;}
.ok{color:#4ade80;} .err{color:#f87171;} .warn{color:#fbbf24;}
table{border-collapse:collapse;width:100%;} td,th{border:1px solid #444;padding:6px 10px;text-align:left;}
th{background:#1e3a5f;} tr:nth-child(even){background:#1a1a1a;}
img{max-width:100px;max-height:60px;border:1px solid #444;}
</style></head><body>';

echo '<h2>🔍 Debug Đường dẫn Ảnh</h2>';

// ── Kết nối DB ───────────────────────────────────────────────────────────────
$db = new PDO('mysql:host=127.0.0.1;port=3307;dbname=dakhoacantho_web;charset=utf8', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// ── Lấy 5 bài viết có thumbnail ──────────────────────────────────────────────
echo '<h3>1. Giá trị thumbnail_image trong DB</h3>';
$articles = $db->query("SELECT id, title, thumbnail_image FROM articles WHERE thumbnail_image IS NOT NULL AND thumbnail_image != '' LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);

echo '<table><tr><th>ID</th><th>Title</th><th>thumbnail_image (DB)</th><th>URL generated</th><th>File tồn tại?</th><th>Preview</th></tr>';

$app_url = 'http://localhost/dakhoacantho_web/public';
$public_path = 'C:/xampp/htdocs/dakhoacantho_web/public';

foreach ($articles as $a) {
    $thumb = $a['thumbnail_image'];
    
    // Tạo URL như Laravel asset('storage/' . $thumb)
    $url = $app_url . '/storage/' . $thumb;
    
    // Kiểm tra file tồn tại
    $file_path = $public_path . '/storage/' . $thumb;
    $exists = file_exists($file_path);
    
    echo '<tr>';
    echo '<td>' . $a['id'] . '</td>';
    echo '<td>' . htmlspecialchars(mb_substr($a['title'], 0, 40)) . '</td>';
    echo '<td style="color:#fbbf24">' . htmlspecialchars($thumb) . '</td>';
    echo '<td><a href="' . $url . '" target="_blank" style="color:#60a5fa;font-size:11px">' . htmlspecialchars($url) . '</a></td>';
    echo '<td class="' . ($exists ? 'ok' : 'err') . '">' . ($exists ? '✅ Có' : '❌ Không') . '</td>';
    echo '<td>' . ($exists ? '<img src="' . $url . '">' : '<span class="err">404</span>') . '</td>';
    echo '</tr>';
}
echo '</table>';

// ── Kiểm tra cấu trúc public/storage ────────────────────────────────────────
echo '<h3>2. Cấu trúc public/storage</h3>';
$ps = $public_path . '/storage';
if (is_link($ps)) {
    echo '<p class="ok">✅ Symlink → ' . readlink($ps) . '</p>';
} elseif (is_dir($ps)) {
    $items = array_diff(scandir($ps), ['.', '..']);
    echo '<p class="warn">⚠️ Thư mục vật lý: ' . implode(', ', $items) . '</p>';
}

// Kiểm tra xem ảnh sample có tồn tại không
$sample_check_paths = [
    '/storage/uploads/2019/01',
    '/storage/uploads/2020/05',
    '/storage/2019/01',
];
echo '<table><tr><th>Path</th><th>Tồn tại?</th><th>Số files</th></tr>';
foreach ($sample_check_paths as $p) {
    $full = $public_path . $p;
    $exists = is_dir($full);
    $count = $exists ? count(array_diff(scandir($full), ['.', '..'])) : 0;
    echo '<tr><td>' . $p . '</td>';
    echo '<td class="' . ($exists ? 'ok' : 'err') . '">' . ($exists ? '✅' : '❌') . '</td>';
    echo '<td>' . $count . '</td></tr>';
}
echo '</table>';

// ── Gợi ý sửa ───────────────────────────────────────────────────────────────
echo '<h3>3. Kết luận</h3>';
if (!empty($articles)) {
    $thumb = $articles[0]['thumbnail_image'];
    $file_path = $public_path . '/storage/' . $thumb;
    if (!file_exists($file_path)) {
        // Tìm file ở vị trí khác
        $alt1 = $public_path . '/storage/uploads/' . $thumb; // nếu DB lưu không có uploads/
        $alt2 = $public_path . '/storage/' . preg_replace('/^uploads\//', '', $thumb); // nếu có uploads/ thừa
        
        echo '<p class="warn">❌ File không tìm thấy tại: <code>' . $file_path . '</code></p>';
        echo '<p>Thử tìm tại: <code>' . $alt1 . '</code> → ' . (file_exists($alt1) ? '<span class="ok">✅ Có!</span>' : '<span class="err">❌ Không</span>') . '</p>';
        echo '<p>Thử tìm tại: <code>' . $alt2 . '</code> → ' . (file_exists($alt2) ? '<span class="ok">✅ Có!</span>' : '<span class="err">❌ Không</span>') . '</p>';
    } else {
        echo '<p class="ok">✅ Ảnh tìm thấy đúng chỗ!</p>';
    }
}

echo '</body></html>';
