<?php
/**
 * fix_images.php — Khôi phục ảnh cho dakhoacantho_web
 * Truy cập: http://localhost/dakhoacantho_web/public/fix_images.php
 * XÓA FILE NÀY SAU KHI HOÀN THÀNH!
 */

set_time_limit(300); // 5 phút
header('Content-Type: text/html; charset=utf-8');

$rtk_src    = 'C:/xampp/htdocs/rtk/storage/app/public';
$dst_uploads = 'C:/xampp/htdocs/dakhoacantho_web/storage/app/public/uploads';
$public_storage = 'C:/xampp/htdocs/dakhoacantho_web/public/storage';
$storage_target = 'C:/xampp/htdocs/dakhoacantho_web/storage/app/public';

echo '<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8">
<title>Fix Images</title>
<style>body{font-family:monospace;padding:20px;background:#111;color:#eee;}
.ok{color:#4ade80;} .err{color:#f87171;} .warn{color:#fbbf24;}
h2{color:#60a5fa;} pre{background:#1e1e1e;padding:10px;border-radius:6px;}
</style></head><body>';

echo '<h2>🔧 Khôi phục ảnh dakhoacantho_web</h2>';

// ─── BƯỚC 1: Kiểm tra nguồn (rtk) ───────────────────────────────────────────
echo '<h3>Bước 1 — Kiểm tra nguồn ảnh từ RTK</h3>';
if (!is_dir($rtk_src)) {
    echo '<p class="err">❌ Không tìm thấy thư mục RTK: ' . $rtk_src . '</p>';
    die();
}
$rtk_items = array_filter(scandir($rtk_src), fn($f) => $f !== '.' && $f !== '..' && $f !== '.gitignore');
echo '<p class="ok">✅ RTK source OK — ' . count($rtk_items) . ' items: ' . implode(', ', $rtk_items) . '</p>';

// ─── BƯỚC 2: Tạo thư mục uploads nếu chưa có ──────────────────────────────
echo '<h3>Bước 2 — Tạo thư mục uploads</h3>';
if (!is_dir($dst_uploads)) {
    if (mkdir($dst_uploads, 0755, true)) {
        echo '<p class="ok">✅ Đã tạo: ' . $dst_uploads . '</p>';
    } else {
        echo '<p class="err">❌ Không thể tạo thư mục: ' . $dst_uploads . '</p>';
        die();
    }
} else {
    echo '<p class="warn">⚠️ Thư mục đã tồn tại: ' . $dst_uploads . '</p>';
}

// ─── BƯỚC 3: Copy ảnh từ RTK ────────────────────────────────────────────────
echo '<h3>Bước 3 — Copy ảnh từ RTK → uploads/</h3>';

function copyDir($src, $dst) {
    if (!is_dir($dst)) mkdir($dst, 0755, true);
    $count = 0;
    $iter = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($src, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($iter as $item) {
        $target = $dst . DIRECTORY_SEPARATOR . $iter->getSubPathname();
        if ($item->isDir()) {
            if (!is_dir($target)) mkdir($target, 0755, true);
        } else {
            copy($item->getRealPath(), $target);
            $count++;
        }
    }
    return $count;
}

$years_and_dirs = array_filter(scandir($rtk_src), function($f) use ($rtk_src) {
    return $f !== '.' && $f !== '..' && $f !== '.gitignore' && is_dir($rtk_src . '/' . $f);
});

$total_copied = 0;
foreach ($years_and_dirs as $dir) {
    $src_path = $rtk_src . '/' . $dir;
    $dst_path = $dst_uploads . '/' . $dir;
    echo '<p>📁 Đang copy <strong>' . $dir . '</strong>...</p>';
    flush();
    ob_flush();
    $n = copyDir($src_path, $dst_path);
    echo '<p class="ok">&nbsp;&nbsp;✅ ' . $dir . ' — ' . $n . ' files</p>';
    flush();
    ob_flush();
    $total_copied += $n;
}

echo '<p class="ok"><strong>✅ Tổng cộng: ' . $total_copied . ' files đã copy</strong></p>';

// ─── BƯỚC 4: Kiểm tra/tạo symlink public/storage ────────────────────────────
echo '<h3>Bước 4 — Tạo symlink public/storage</h3>';

// Nếu public/storage là thư mục thật (không phải symlink), xóa đi
if (is_dir($public_storage) && !is_link($public_storage)) {
    // Chỉ xóa nếu trống hoặc chỉ có .gitignore
    $items = array_diff(scandir($public_storage), ['.', '..', '.gitignore']);
    if (empty($items)) {
        // Xóa .gitignore nếu có
        if (file_exists($public_storage . '/.gitignore')) {
            unlink($public_storage . '/.gitignore');
        }
        rmdir($public_storage);
        echo '<p class="ok">✅ Đã xóa thư mục public/storage rỗng cũ</p>';
    } else {
        echo '<p class="warn">⚠️ public/storage có ' . count($items) . ' item — bỏ qua xóa</p>';
    }
}

// Tạo symlink
if (!file_exists($public_storage) && !is_link($public_storage)) {
    if (symlink($storage_target, $public_storage)) {
        echo '<p class="ok">✅ Symlink tạo thành công: public/storage → storage/app/public</p>';
    } else {
        echo '<p class="err">❌ Không thể tạo symlink! Trên Windows cần quyền Admin hoặc Developer Mode.</p>';
        echo '<p class="warn">💡 Thay thế: Tạo Junction (không cần quyền admin)...</p>';
        // Thử tạo Junction thay thế
        $output = [];
        $retval = 0;
        exec('mklink /J "' . str_replace('/', '\\', $public_storage) . '" "' . str_replace('/', '\\', $storage_target) . '"', $output, $retval);
        if ($retval === 0) {
            echo '<p class="ok">✅ Junction tạo thành công!</p>';
        } else {
            echo '<p class="err">❌ Junction cũng thất bại: ' . implode(' ', $output) . '</p>';
            echo '<p class="warn">⚠️ Cần chạy: <code>mklink /J "' . str_replace('/', '\\', $public_storage) . '" "' . str_replace('/', '\\', $storage_target) . '"</code> trong CMD Admin</p>';
        }
    }
} elseif (is_link($public_storage)) {
    echo '<p class="ok">✅ Symlink đã tồn tại: ' . readlink($public_storage) . '</p>';
}

// ─── BƯỚC 5: Kiểm tra ảnh có truy cập được không ────────────────────────────
echo '<h3>Bước 5 — Kiểm tra kết quả</h3>';
$test_upload_path = $public_storage . '/uploads';
if (is_dir($test_upload_path)) {
    $years = array_filter(scandir($test_upload_path), fn($f) => is_dir($test_upload_path . '/' . $f) && $f !== '.' && $f !== '..');
    echo '<p class="ok">✅ public/storage/uploads có: ' . implode(', ', $years) . '</p>';
} else {
    echo '<p class="err">❌ Không tìm thấy public/storage/uploads</p>';
}

echo '<hr>';
echo '<p class="warn">⚠️ <strong>XÓA FILE fix_images.php SAU KHI HOÀN THÀNH!</strong></p>';
echo '<p><a href="/dakhoacantho_web/public/" style="color:#60a5fa">→ Quay lại trang chủ</a></p>';
echo '</body></html>';
