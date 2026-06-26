<?php
/**
 * fix_production_storage.php
 * ============================================================
 * Mục đích:
 *   1. Di chuyển public/storage/uploads/ → storage/app/public/uploads/
 *   2. Xóa thư mục vật lý public/storage/
 *   3. Tạo symlink đúng: public/storage → ../storage/app/public
 *   4. Tạo storage/.htaccess để chặn truy cập công khai vào
 *      storage/logs/, storage/framework/, storage/backups/
 *
 * ⚠️  XÓA FILE NÀY NGAY SAU KHI CHẠY XONG!
 * ============================================================
 */

set_time_limit(300);
header('Content-Type: text/html; charset=utf-8');

function printLog(string $msg, string $type = 'info'): void {
    $class = match($type) {
        'ok'   => 'ok',
        'err'  => 'err',
        'warn' => 'warn',
        default => 'info',
    };
    echo "<p class=\"{$class}\">{$msg}</p>\n";
    flush();
}

function rrmdir(string $dir): bool {
    if (!is_dir($dir)) return true;
    $items = array_diff(scandir($dir), ['.', '..']);
    foreach ($items as $item) {
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        if (is_dir($path) && !is_link($path)) {
            rrmdir($path);
        } else {
            unlink($path);
        }
    }
    return rmdir($dir);
}

function rcopy(string $src, string $dst): bool {
    if (!is_dir($dst)) {
        if (!mkdir($dst, 0755, true)) {
            return false;
        }
    }
    $items = array_diff(scandir($src), ['.', '..']);
    foreach ($items as $item) {
        $srcPath = $src . DIRECTORY_SEPARATOR . $item;
        $dstPath = $dst . DIRECTORY_SEPARATOR . $item;
        if (is_dir($srcPath)) {
            if (!rcopy($srcPath, $dstPath)) return false;
        } else {
            if (!copy($srcPath, $dstPath)) return false;
        }
    }
    return true;
}

echo <<<HTML
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title>Fix Production Storage</title>
<style>
  body { font-family: monospace; padding: 20px; background: #111; color: #eee; max-width: 900px; margin: 0 auto; }
  .ok   { color: #4ade80; }
  .err  { color: #f87171; }
  .warn { color: #fbbf24; }
  .info { color: #93c5fd; }
  h2 { color: #60a5fa; border-bottom: 1px solid #334155; padding-bottom: 6px; }
  h3 { color: #a78bfa; }
  code { background: #1e1e1e; padding: 2px 8px; border-radius: 4px; color: #f0abfc; }
  pre  { background: #1e1e1e; padding: 12px; border-radius: 6px; overflow-x: auto; color: #d1fae5; }
  .box { background: #1e293b; border: 1px solid #334155; border-radius: 8px; padding: 12px 16px; margin: 12px 0; }
  hr   { border-color: #334155; }
</style>
</head>
<body>
<h2>🔧 Fix Production Storage — dakhoacantho.com</h2>
HTML;

// ─────────────────────────────────────────────────────────────────────────────
// Xác định đường dẫn tự động (không hard-code)
// ─────────────────────────────────────────────────────────────────────────────
$publicDir       = realpath(__DIR__);                          // public_html/public
$appRoot         = realpath($publicDir . '/..');               // public_html  (Laravel root)
$publicStorage   = $publicDir . '/storage';                    // public_html/public/storage
$laravelStorage  = $appRoot . '/storage';                      // public_html/storage
$storageAppPublic = $laravelStorage . '/app/public';           // public_html/storage/app/public
$symlinkTarget   = '../storage/app/public';                    // relative path for symlink

echo "<div class=\"box\">";
printLog("📁 App root       : <code>{$appRoot}</code>", 'info');
printLog("📁 Public dir     : <code>{$publicDir}</code>", 'info');
printLog("📁 public/storage : <code>{$publicStorage}</code>", 'info');
printLog("📁 storage/app/pub: <code>{$storageAppPublic}</code>", 'info');
echo "</div>";

// ─────────────────────────────────────────────────────────────────────────────
// BƯỚC 1: Kiểm tra trạng thái hiện tại
// ─────────────────────────────────────────────────────────────────────────────
echo '<h3>Bước 1 — Kiểm tra trạng thái hiện tại</h3>';

if (is_link($publicStorage)) {
    $target = readlink($publicStorage);
    printLog("🔗 public/storage là symlink → <code>{$target}</code>", 'warn');
    $isSymlink = true;
    $isDir     = false;
} elseif (is_dir($publicStorage)) {
    $items = array_diff(scandir($publicStorage), ['.', '..']);
    printLog("📂 public/storage là thư mục vật lý với " . count($items) . " items: <code>" . implode(', ', $items) . "</code>", 'warn');
    $isSymlink = false;
    $isDir     = true;
} else {
    printLog("❌ public/storage không tồn tại", 'err');
    $isSymlink = false;
    $isDir     = false;
}

// Kiểm tra uploads
$uploadsInPublicStorage  = $publicStorage . '/uploads';
$uploadsInStorageAppPublic = $storageAppPublic . '/uploads';

if (is_dir($uploadsInPublicStorage) && !is_link($publicStorage)) {
    $count = iterator_count(
        new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($uploadsInPublicStorage, RecursiveDirectoryIterator::SKIP_DOTS)
        )
    );
    printLog("📦 Tìm thấy uploads tại <code>public/storage/uploads/</code> — {$count} files", 'warn');
} else {
    printLog("ℹ️  Không có uploads trong public/storage/uploads/", 'info');
}

// ─────────────────────────────────────────────────────────────────────────────
// BƯỚC 2: Di chuyển uploads nếu cần
// ─────────────────────────────────────────────────────────────────────────────
echo '<h3>Bước 2 — Di chuyển uploads → storage/app/public/uploads/</h3>';

if (!$isSymlink && $isDir && is_dir($uploadsInPublicStorage)) {
    if (is_dir($uploadsInStorageAppPublic)) {
        printLog("ℹ️  storage/app/public/uploads/ đã tồn tại — sẽ merge thay vì ghi đè", 'warn');
    }

    printLog("⏳ Đang copy uploads... (có thể mất vài phút nếu nhiều file)", 'info');
    flush();

    if (rcopy($uploadsInPublicStorage, $uploadsInStorageAppPublic)) {
        printLog("✅ Copy uploads thành công!", 'ok');

        // Xóa thư mục uploads gốc sau khi copy thành công
        if (rrmdir($uploadsInPublicStorage)) {
            printLog("✅ Đã xóa public/storage/uploads/ gốc", 'ok');
        } else {
            printLog("⚠️  Không xóa được public/storage/uploads/ gốc — xóa thủ công nếu cần", 'warn');
        }
    } else {
        printLog("❌ Copy uploads THẤT BẠI — dừng lại để tránh mất dữ liệu", 'err');
        echo '</body></html>';
        exit;
    }
} else {
    printLog("ℹ️  Không cần di chuyển uploads", 'info');
}

// ─────────────────────────────────────────────────────────────────────────────
// BƯỚC 3: Xóa thư mục vật lý public/storage/
// ─────────────────────────────────────────────────────────────────────────────
echo '<h3>Bước 3 — Xóa thư mục vật lý public/storage/</h3>';

if (!$isSymlink && $isDir) {
    // Xóa bất kỳ file còn sót lại
    if (rrmdir($publicStorage)) {
        printLog("✅ Đã xóa thư mục vật lý public/storage/", 'ok');
    } else {
        printLog("❌ Không xóa được public/storage/ — có thể còn file bên trong. Kiểm tra thủ công.", 'err');
        echo '</body></html>';
        exit;
    }
} elseif ($isSymlink) {
    // Xóa symlink cũ (trỏ sai)
    if (unlink($publicStorage)) {
        printLog("✅ Đã xóa symlink cũ (trỏ sai)", 'ok');
    } else {
        printLog("❌ Không xóa được symlink cũ", 'err');
        echo '</body></html>';
        exit;
    }
} else {
    printLog("ℹ️  public/storage không tồn tại — sẽ tạo symlink mới", 'info');
}

// ─────────────────────────────────────────────────────────────────────────────
// BƯỚC 4: Tạo symlink đúng
// ─────────────────────────────────────────────────────────────────────────────
echo '<h3>Bước 4 — Tạo symlink: public/storage → ../storage/app/public</h3>';

// Đảm bảo thư mục target tồn tại
if (!is_dir($storageAppPublic)) {
    mkdir($storageAppPublic, 0755, true);
    printLog("✅ Đã tạo thư mục storage/app/public/", 'ok');
}

if (symlink($symlinkTarget, $publicStorage)) {
    printLog("✅ Symlink tạo thành công: <code>public/storage</code> → <code>{$symlinkTarget}</code>", 'ok');
} else {
    printLog("❌ PHP symlink thất bại — thử absolute path...", 'err');
    if (symlink($storageAppPublic, $publicStorage)) {
        printLog("✅ Symlink absolute path thành công!", 'ok');
    } else {
        printLog("❌ Cả hai cách đều thất bại. Cần chạy lệnh sau qua SSH hoặc cPanel Terminal:", 'err');
        echo "<pre>cd {$publicDir}\nln -s ../storage/app/public storage</pre>";
        echo '</body></html>';
        exit;
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// BƯỚC 5: Tạo .htaccess bảo mật cho storage/
// ─────────────────────────────────────────────────────────────────────────────
echo '<h3>Bước 5 — Tạo storage/.htaccess để bảo vệ thư mục nhạy cảm</h3>';

// .htaccess cho storage/ gốc — chặn tất cả truy cập trực tiếp
$storageHtaccess = $laravelStorage . '/.htaccess';
$storageHtaccessContent = <<<HTACCESS
# Chặn tất cả truy cập trực tiếp vào storage/
Options -Indexes
<IfModule mod_authz_core.c>
    Require all denied
</IfModule>
<IfModule !mod_authz_core.c>
    Order deny,allow
    Deny from all
</IfModule>
HTACCESS;

if (file_put_contents($storageHtaccess, $storageHtaccessContent) !== false) {
    printLog("✅ Tạo <code>storage/.htaccess</code> — chặn truy cập vào storage/ gốc", 'ok');
} else {
    printLog("⚠️  Không tạo được storage/.htaccess — tạo thủ công", 'warn');
}

// .htaccess cho storage/app/ — chặn, nhưng cho phép public/ thông qua symlink
$storageAppHtaccess = $laravelStorage . '/app/.htaccess';
$storageAppHtaccessContent = <<<HTACCESS
Options -Indexes
<IfModule mod_authz_core.c>
    Require all denied
</IfModule>
<IfModule !mod_authz_core.c>
    Order deny,allow
    Deny from all
</IfModule>
HTACCESS;

if (file_put_contents($storageAppHtaccess, $storageAppHtaccessContent) !== false) {
    printLog("✅ Tạo <code>storage/app/.htaccess</code>", 'ok');
} else {
    printLog("⚠️  Không tạo được storage/app/.htaccess", 'warn');
}

// .htaccess riêng cho logs/
$logsHtaccess = $laravelStorage . '/logs/.htaccess';
$logsHtaccessContent = <<<HTACCESS
Options -Indexes
<IfModule mod_authz_core.c>
    Require all denied
</IfModule>
<IfModule !mod_authz_core.c>
    Order deny,allow
    Deny from all
</IfModule>
HTACCESS;

if (file_put_contents($logsHtaccess, $logsHtaccessContent) !== false) {
    printLog("✅ Tạo <code>storage/logs/.htaccess</code> — bảo vệ log files", 'ok');
} else {
    printLog("⚠️  Không tạo được storage/logs/.htaccess", 'warn');
}

// .htaccess cho framework/
$frameworkHtaccess = $laravelStorage . '/framework/.htaccess';
if (is_dir($laravelStorage . '/framework')) {
    if (file_put_contents($frameworkHtaccess, $logsHtaccessContent) !== false) {
        printLog("✅ Tạo <code>storage/framework/.htaccess</code>", 'ok');
    } else {
        printLog("⚠️  Không tạo được storage/framework/.htaccess", 'warn');
    }
}

// ─────────────────────────────────────────────────────────────────────────────
// BƯỚC 6: Kiểm tra kết quả
// ─────────────────────────────────────────────────────────────────────────────
echo '<h3>Bước 6 — Kiểm tra kết quả</h3>';

// Kiểm tra symlink
if (is_link($publicStorage)) {
    $resolved = realpath($publicStorage);
    printLog("✅ public/storage là symlink → <code>" . readlink($publicStorage) . "</code> (resolved: {$resolved})", 'ok');
} else {
    printLog("❌ public/storage vẫn không phải symlink!", 'err');
}

// Kiểm tra uploads có thể truy cập qua symlink
if (is_dir($publicStorage . '/uploads')) {
    $files = iterator_count(
        new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($publicStorage . '/uploads', RecursiveDirectoryIterator::SKIP_DOTS)
        )
    );
    printLog("✅ uploads/ truy cập được qua symlink — {$files} files", 'ok');
} else {
    printLog("⚠️  uploads/ không tìm thấy trong storage/app/public/ — kiểm tra lại", 'warn');
}

// Kiểm tra avatars directory
if (is_dir($publicStorage . '/avatars')) {
    printLog("✅ avatars/ tồn tại trong storage/app/public/", 'ok');
} else {
    printLog("ℹ️  avatars/ chưa tồn tại — sẽ được tạo tự động khi user upload ảnh đại diện đầu tiên", 'info');
    // Tạo sẵn thư mục avatars
    if (mkdir($storageAppPublic . '/avatars', 0755, true)) {
        printLog("✅ Đã tạo sẵn thư mục <code>storage/app/public/avatars/</code>", 'ok');
    }
}

// Kiểm tra category-banners
if (is_dir($publicStorage . '/category-banners')) {
    printLog("✅ category-banners/ truy cập được", 'ok');
}

echo <<<HTML
<hr>
<h3>✅ Hoàn tất!</h3>
<div class="box">
<p class="ok">Tất cả các bước đã thực hiện thành công. Tiếp theo:</p>
<p class="warn">⚠️ <strong>XÓA FILE NÀY NGAY BÂY GIỜ!</strong> → <code>public/fix_production_storage.php</code></p>
<p class="info">Kiểm tra ảnh đại diện tại trang admin → Người dùng</p>
<p class="info">Kiểm tra bảo mật: <a href="/storage/logs/" style="color:#f87171">https://dakhoacantho.com/storage/logs/</a> phải trả về 403 Forbidden</p>
<p class="info">Kiểm tra ảnh: <a href="/storage/uploads/" style="color:#4ade80">https://dakhoacantho.com/storage/uploads/</a> phải hoạt động bình thường</p>
</div>
</body>
</html>
HTML;
