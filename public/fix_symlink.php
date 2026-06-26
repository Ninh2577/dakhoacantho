<?php
/**
 * fix_symlink.php — Sửa symlink public/storage cho dakhoacantho_web
 * Truy cập: http://localhost/dakhoacantho_web/public/fix_symlink.php
 * XÓA FILE NÀY SAU KHI HOÀN THÀNH!
 */

set_time_limit(60);
header('Content-Type: text/html; charset=utf-8');
echo '<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8"><title>Fix Symlink</title>
<style>body{font-family:monospace;padding:20px;background:#111;color:#eee;}
.ok{color:#4ade80;} .err{color:#f87171;} .warn{color:#fbbf24;}
h2{color:#60a5fa;} code{background:#1e1e1e;padding:2px 6px;border-radius:4px;color:#f0f;}
</style></head><body>';

echo '<h2>🔧 Sửa symlink public/storage</h2>';

$public_storage = 'C:/xampp/htdocs/dakhoacantho_web/public/storage';
$storage_target = 'C:\\xampp\\htdocs\\dakhoacantho_web\\storage\\app\\public';
$storage_target_fwd = 'C:/xampp/htdocs/dakhoacantho_web/storage/app/public';

// ── Kiểm tra trạng thái hiện tại ──────────────────────────────────────────
echo '<h3>Trạng thái hiện tại</h3>';

if (is_link($public_storage)) {
    echo '<p class="ok">✅ public/storage là symlink → ' . readlink($public_storage) . '</p>';
} elseif (is_dir($public_storage)) {
    $items = array_diff(scandir($public_storage), ['.', '..']);
    echo '<p class="warn">⚠️ public/storage là thư mục vật lý với ' . count($items) . ' items: ' . implode(', ', $items) . '</p>';
} else {
    echo '<p class="err">❌ public/storage không tồn tại</p>';
}

// ── Kiểm tra target ────────────────────────────────────────────────────────
if (is_dir($storage_target_fwd)) {
    $contents = array_diff(scandir($storage_target_fwd), ['.', '..', '.gitignore']);
    echo '<p class="ok">✅ Target tồn tại: ' . $storage_target_fwd . ' (' . count($contents) . ' items: ' . implode(', ', array_slice(array_values($contents), 0, 5)) . '...)</p>';
} else {
    echo '<p class="err">❌ Target không tồn tại: ' . $storage_target_fwd . '</p>';
    die();
}

// ── Bước 1: Xóa thư mục public/storage vật lý ────────────────────────────
echo '<h3>Bước 1 — Xóa thư mục public/storage vật lý</h3>';

if (is_dir($public_storage) && !is_link($public_storage)) {
    // Xóa nội dung bên trong trước (chỉ gitignore và uploads junction)
    $gitignore = $public_storage . '/.gitignore';
    if (file_exists($gitignore)) {
        unlink($gitignore);
        echo '<p class="ok">✅ Đã xóa .gitignore</p>';
    }

    // Xóa junction/symlink uploads bên trong nếu có
    $uploadsLink = $public_storage . '/uploads';
    if (is_link($uploadsLink)) {
        // Windows symlink/junction
        $out = []; $ret = 0;
        exec('rmdir "' . str_replace('/', '\\', $uploadsLink) . '"', $out, $ret);
        echo '<p class="' . ($ret === 0 ? 'ok' : 'warn') . '">' . ($ret === 0 ? '✅' : '⚠️') . ' Xóa uploads junction: ' . implode(' ', $out) . '</p>';
    }

    // Xóa thư mục storage rỗng
    $out = []; $ret = 0;
    exec('rmdir "' . str_replace('/', '\\', $public_storage) . '"', $out, $ret);
    if ($ret === 0) {
        echo '<p class="ok">✅ Đã xóa thư mục public/storage vật lý</p>';
    } else {
        echo '<p class="err">❌ Không thể xóa public/storage: ' . implode(' ', $out) . '</p>';
        echo '<p class="warn">Thử cách khác...</p>';
        // Force remove via rmdir /s
        exec('rmdir /s /q "' . str_replace('/', '\\', $public_storage) . '"', $out, $ret);
        echo '<p class="' . ($ret === 0 ? 'ok' : 'err') . '">' . ($ret === 0 ? '✅' : '❌') . ' rmdir /s /q: ret=' . $ret . '</p>';
    }
}

// ── Bước 2: Tạo Junction (không cần admin trên Windows) ───────────────────
echo '<h3>Bước 2 — Tạo Junction public/storage</h3>';

if (!file_exists($public_storage) && !is_link($public_storage)) {
    $out = []; $ret = 0;
    $cmd = 'mklink /J "' . str_replace('/', '\\', $public_storage) . '" "' . $storage_target . '"';
    echo '<p>Chạy: <code>' . htmlspecialchars($cmd) . '</code></p>';
    exec($cmd, $out, $ret);
    
    if ($ret === 0) {
        echo '<p class="ok">✅ Junction tạo thành công!</p>';
        echo '<p>' . implode('<br>', $out) . '</p>';
    } else {
        echo '<p class="err">❌ Thất bại (ret=' . $ret . '): ' . implode(' ', $out) . '</p>';
        // Thử symlink PHP
        echo '<p class="warn">Thử symlink PHP...</p>';
        if (symlink($storage_target_fwd, $public_storage)) {
            echo '<p class="ok">✅ PHP symlink thành công!</p>';
        } else {
            echo '<p class="err">❌ PHP symlink cũng thất bại. Cần chạy CMD Admin.</p>';
            echo '<pre class="warn">Chạy lệnh này trong CMD Admin:
mklink /J "C:\xampp\htdocs\dakhoacantho_web\public\storage" "C:\xampp\htdocs\dakhoacantho_web\storage\app\public"</pre>';
        }
    }
} else {
    echo '<p class="warn">⚠️ public/storage vẫn còn tồn tại — kiểm tra thủ công</p>';
}

// ── Bước 3: Kiểm tra kết quả ──────────────────────────────────────────────
echo '<h3>Bước 3 — Kiểm tra kết quả</h3>';

if (is_link($public_storage) || is_dir($public_storage)) {
    $check_path = $public_storage . '/uploads/2019';
    if (is_dir($check_path)) {
        $files = array_diff(scandir($check_path), ['.', '..']);
        echo '<p class="ok">✅ public/storage/uploads/2019 truy cập OK — ' . count($files) . ' tháng</p>';
        
        // Test URL
        echo '<p class="ok">✅ Test URL ảnh: <a href="/dakhoacantho_web/public/storage/uploads/2019/01/" style="color:#60a5fa" target="_blank">/storage/uploads/2019/01/</a></p>';
    } else {
        echo '<p class="err">❌ Không truy cập được public/storage/uploads/2019</p>';
    }
    
    if (is_link($public_storage)) {
        echo '<p class="ok">✅ Symlink target: ' . readlink($public_storage) . '</p>';
    } else {
        echo '<p class="warn">⚠️ Vẫn là thư mục vật lý (có thể là Junction — OK trên Windows)</p>';
    }
} else {
    echo '<p class="err">❌ public/storage không tồn tại</p>';
}

echo '<hr>';
echo '<p><a href="/dakhoacantho_web/public/nam-khoa/bao-quy-dau/cat-bao-quy-dau" style="color:#60a5fa" target="_blank">→ Kiểm tra trang bài viết</a></p>';
echo '<p class="warn">⚠️ <strong>XÓA fix_symlink.php sau khi xong!</strong></p>';
echo '</body></html>';
