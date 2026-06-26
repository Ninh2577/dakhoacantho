<?php
header('Content-Type: text/html; charset=utf-8');
$db = new PDO('mysql:host=127.0.0.1;port=3307;dbname=dakhoacantho_web;charset=utf8', 'root', '');
$app_url    = 'http://localhost/dakhoacantho_web/public';
$public_path = 'C:/xampp/htdocs/dakhoacantho_web/public';

// Lấy các bài viết mới nhất (có và không có ảnh)
$articles = $db->query("
    SELECT id, title, thumbnail_image, featured_image
    FROM articles
    ORDER BY id DESC
    LIMIT 20
")->fetchAll(PDO::FETCH_ASSOC);

echo '<style>body{font-family:monospace;padding:16px;background:#111;color:#eee;}
table{border-collapse:collapse;width:100%;} td,th{border:1px solid #444;padding:6px 10px;}
th{background:#1e3a5f;} .ok{color:#4ade80;} .err{color:#f87171;} .warn{color:#fbbf24;}
img{max-width:80px;max-height:50px;} code{background:#222;padding:2px 5px;color:#fbbf24;}
</style>';
echo '<h2>Đường dẫn ảnh 20 bài viết mới nhất</h2>';
echo '<table><tr><th>ID</th><th>Tiêu đề</th><th>thumbnail_image (DB)</th><th>File tồn tại?</th><th>Preview</th></tr>';

foreach ($articles as $a) {
    $img  = $a['thumbnail_image'] ?: $a['featured_image'];
    $url  = $img ? $app_url . '/storage/' . $img : '';
    $path = $img ? $public_path . '/storage/' . $img : '';
    $exists = $path && file_exists($path);

    echo '<tr>';
    echo '<td>' . $a['id'] . '</td>';
    echo '<td style="font-size:12px">' . htmlspecialchars(mb_substr($a['title'] ?? '', 0, 50)) . '</td>';
    echo '<td><code>' . htmlspecialchars($img ?: '—') . '</code></td>';
    if ($img) {
        echo '<td class="' . ($exists ? 'ok' : 'err') . '">' . ($exists ? '✅ Có' : '❌ Không') . '</td>';
        echo '<td>' . ($exists ? '<img src="' . $url . '">' : '<a href="'.$url.'" target="_blank" style="color:#60a5fa;font-size:10px">Test link</a>') . '</td>';
    } else {
        echo '<td class="warn">— Không có ảnh</td><td>—</td>';
    }
    echo '</tr>';
}
echo '</table>';
