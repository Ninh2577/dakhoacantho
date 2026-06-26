<?php
/**
 * fix_htaccess.php
 * ============================================================
 * Sửa cấu hình .htaccess để cho phép truy cập ảnh từ symlink
 * nhưng vẫn bảo vệ nghiêm ngặt các thư mục nhạy cảm (logs, framework, backups).
 * ============================================================
 */

header('Content-Type: text/html; charset=utf-8');

$appRoot = realpath(__DIR__ . '/..');
$storageDir = $appRoot . '/storage';

echo '<h2>🔧 Sửa cấu hình bảo mật .htaccess trên Production</h2>';

// 1. Cho phép truy cập vào storage/ gốc nhưng cấm xem danh sách file (Options -Indexes)
$storageHtaccess = $storageDir . '/.htaccess';
$storageHtaccessContent = "Options -Indexes\n"; // Chỉ chặn liệt kê file, không chặn truy cập trực tiếp file con qua symlink

if (file_put_contents($storageHtaccess, $storageHtaccessContent) !== false) {
    echo "<p style='color:green'>✅ Đã cập nhật {$storageHtaccess} (Cho phép đọc file qua symlink)</p>";
} else {
    echo "<p style='color:red'>❌ Lỗi cập nhật {$storageHtaccess}</p>";
}

// 2. Cho phép truy cập vào storage/app nhưng cấm xem danh sách file
$storageAppHtaccess = $storageDir . '/app/.htaccess';
if (file_put_contents($storageAppHtaccess, $storageHtaccessContent) !== false) {
    echo "<p style='color:green'>✅ Đã cập nhật {$storageAppHtaccess}</p>";
} else {
    echo "<p style='color:red'>❌ Lỗi cập nhật {$storageAppHtaccess}</p>";
}

// 3. Chặn tuyệt đối truy cập vào logs/
$denyHtaccessContent = <<<HTACCESS
Options -Indexes
<IfModule mod_authz_core.c>
    Require all denied
</IfModule>
<IfModule !mod_authz_core.c>
    Order deny,allow
    Deny from all
</IfModule>
HTACCESS;

$logsHtaccess = $storageDir . '/logs/.htaccess';
if (file_put_contents($logsHtaccess, $denyHtaccessContent) !== false) {
    echo "<p style='color:green'>✅ Đã bảo vệ nghiêm ngặt thư mục logs/ (Require all denied)</p>";
} else {
    echo "<p style='color:red'>❌ Lỗi bảo vệ logs/</p>";
}

// 4. Chặn tuyệt đối truy cập vào framework/
$frameworkHtaccess = $storageDir . '/framework/.htaccess';
if (file_put_contents($frameworkHtaccess, $denyHtaccessContent) !== false) {
    echo "<p style='color:green'>✅ Đã bảo vệ nghiêm ngặt thư mục framework/</p>";
} else {
    echo "<p style='color:red'>❌ Lỗi bảo vệ framework/</p>";
}

// 5. Chặn tuyệt đối truy cập vào backups/
$backupsHtaccess = $storageDir . '/backups/.htaccess';
if (is_dir($storageDir . '/backups')) {
    if (file_put_contents($backupsHtaccess, $denyHtaccessContent) !== false) {
        echo "<p style='color:green'>✅ Đã bảo vệ nghiêm ngặt thư mục backups/</p>";
    } else {
        echo "<p style='color:red'>❌ Lỗi bảo vệ backups/</p>";
    }
}

echo "<h3>🎉 Cấu hình hoàn tất!</h3>";
echo "<p>Hãy thử tải lại trang ảnh đại diện của bạn.</p>";
echo "<p>⚠️ <strong>LƯU Ý:</strong> Xóa file <code>public/fix_htaccess.php</code> sau khi hoàn tất!</p>";
