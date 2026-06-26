<?php
/**
 * compress_existing_images.php
 * ============================================================
 * Quét và nén hàng loạt ảnh cũ hiện có trong thư mục storage/app/public
 * để giải phóng dung lượng đĩa trên hosting.
 *
 * ⚠️ HÃY XÓA FILE NÀY SAU KHI SỬ DỤNG XONG!
 * ============================================================
 */

set_time_limit(300); // 5 phút cho mỗi đợt chạy
ini_set('memory_limit', '256M');

header('Content-Type: text/html; charset=utf-8');

$appRoot = realpath(__DIR__ . '/..');
$storageDir = $appRoot . '/storage/app/public';

// Kiểm tra xem thư viện GD có được cài đặt hay không
if (!extension_loaded('gd')) {
    die("<h2 style='color:red'>❌ Lỗi: Thư viện PHP GD chưa được kích hoạt trên máy chủ này!</h2>");
}

// Lấy tham số điều khiển
$batchSize = isset($_GET['batch']) ? (int)$_GET['batch'] : 100;
$offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
$autoRun = isset($_GET['autorun']) ? (bool)$_GET['autorun'] : false;
$compressQuality = 75; // Chất lượng nén JPG/WebP (0-100)

// Quét toàn bộ file ảnh đệ quy trong thư mục lưu trữ
function getImagesRecursive($dir) {
    $images = [];
    if (!is_dir($dir)) return $images;
    
    $iter = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );
    
    foreach ($iter as $file) {
        if ($file->isFile()) {
            $ext = strtolower($file->getExtension());
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                // Kiểm tra xem file có thực sự là ảnh và không bị rỗng
                if ($file->getSize() > 0) {
                    $images[] = [
                        'path' => $file->getRealPath(),
                        'size' => $file->getSize(),
                        'extension' => $ext
                    ];
                }
            }
        }
    }
    return $images;
}

// Nén một tệp ảnh và lưu đè lên chính nó
function compressImage($filePath, $ext, $quality = 75) {
    $originalSize = filesize($filePath);
    if ($originalSize === 0) return false;

    // Tránh nén lại các file đã quá nhỏ (< 20KB)
    if ($originalSize < 20 * 1024) {
        return ['status' => 'skipped', 'reason' => 'Kích thước nhỏ (<20KB)', 'saved' => 0];
    }

    try {
        switch ($ext) {
            case 'jpg':
            case 'jpeg':
                $image = @imagecreatefromjpeg($filePath);
                if (!$image) return false;
                
                // Lưu đè
                imagejpeg($image, $filePath, $quality);
                imagedestroy($image);
                break;
                
            case 'webp':
                $image = @imagecreatefromwebp($filePath);
                if (!$image) return false;
                
                // Lưu đè
                imagewebp($image, $filePath, $quality);
                imagedestroy($image);
                break;
                
            case 'png':
                $image = @imagecreatefrompng($filePath);
                if (!$image) return false;
                
                // Bảo toàn kênh alpha (độ trong suốt) cho ảnh PNG
                imagealphablending($image, false);
                imagesavealpha($image, true);
                
                // Nén PNG (quality từ 0-9 trong imagepng, 9 là nén cao nhất nhưng ko mất chất lượng)
                // Ta dùng mức nén 8 để cân bằng hiệu năng và dung lượng
                imagepng($image, $filePath, 8);
                imagedestroy($image);
                break;
                
            default:
                return false;
        }
        
        // Xóa cache trạng thái file để đọc đúng dung lượng mới
        clearstatcache(true, $filePath);
        $newSize = filesize($filePath);
        $saved = $originalSize - $newSize;
        
        if ($saved > 0) {
            return ['status' => 'success', 'original' => $originalSize, 'new' => $newSize, 'saved' => $saved];
        } else {
            // Nếu nén xong mà dung lượng lớn hơn hoặc bằng cũ, khôi phục lại (trong GD hiếm khi xảy ra nhưng đề phòng)
            return ['status' => 'skipped', 'reason' => 'Không giảm dung lượng', 'saved' => 0];
        }
    } catch (Exception $e) {
        return ['status' => 'error', 'reason' => $e->getMessage(), 'saved' => 0];
    }
}

// Định dạng hiển thị dung lượng file
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

// Bắt đầu quét ảnh
$allImages = getImagesRecursive($storageDir);
$totalCount = count($allImages);
$slicedImages = array_slice($allImages, $offset, $batchSize);
$currentBatchCount = count($slicedImages);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Nén ảnh hàng loạt (Production)</title>
    <style>
        body { font-family: monospace; background: #111; color: #eee; padding: 20px; max-width: 1000px; margin: 0 auto; }
        h1, h2 { color: #60a5fa; border-bottom: 1px solid #334155; padding-bottom: 8px; }
        .stats-box { display: flex; gap: 20px; margin-bottom: 20px; background: #1e293b; padding: 15px; border-radius: 8px; border: 1px solid #334155; }
        .stat-item { flex: 1; text-align: center; }
        .stat-value { font-size: 24px; font-weight: bold; color: #4ade80; }
        .stat-label { font-size: 12px; color: #94a3b8; margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 12px; }
        th, td { border: 1px solid #334155; padding: 8px 10px; text-align: left; }
        th { background: #1e3a8a; color: #fff; }
        tr:nth-child(even) { background: #1e293b; }
        .status-ok { color: #4ade80; font-weight: bold; }
        .status-skip { color: #fbbf24; }
        .status-err { color: #f87171; }
        .progress-container { width: 100%; background: #334155; border-radius: 6px; height: 20px; margin: 15px 0; overflow: hidden; position: relative; }
        .progress-bar { height: 100%; background: #3b82f6; width: 0%; transition: width 0.3s; }
        .progress-text { position: absolute; width: 100%; text-align: center; font-size: 12px; line-height: 20px; font-weight: bold; color: #fff; }
        .btn { display: inline-block; background: #2563eb; color: #fff; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; text-decoration: none; font-size: 14px; font-weight: bold; }
        .btn:hover { background: #1d4ed8; }
        .btn-stop { background: #dc2626; }
        .btn-stop:hover { background: #b91c1c; }
        .log-box { max-height: 300px; overflow-y: auto; background: #000; border: 1px solid #334155; border-radius: 6px; padding: 10px; }
    </style>
</head>
<body>

<h1>⚡ Công cụ nén ảnh hàng loạt (GD Library)</h1>

<div class="stats-box">
    <div class="stat-item">
        <div class="stat-value"><?= number_format($totalCount) ?></div>
        <div class="stat-label">Tổng số ảnh tìm thấy</div>
    </div>
    <div class="stat-item">
        <div class="stat-value"><?= number_format($offset) ?></div>
        <div class="stat-label">Đã duyệt qua</div>
    </div>
    <div class="stat-item">
        <div class="stat-value"><?= number_format(min($offset + $batchSize, $totalCount)) ?> / <?= number_format($totalCount) ?></div>
        <div class="stat-label">Tiến trình hiện tại</div>
    </div>
</div>

<?php
$percent = $totalCount > 0 ? round(($offset / $totalCount) * 100, 1) : 100;
?>
<div class="progress-container">
    <div class="progress-bar" style="width: <?= $percent ?>%"></div>
    <div class="progress-text"><?= $percent ?>% (<?= $offset ?> / <?= $totalCount ?>)</div>
</div>

<div style="margin-bottom: 20px;">
    <?php if ($offset < $totalCount): ?>
        <?php if ($autoRun): ?>
            <a href="?offset=<?= $offset ?>&batch=<?= $batchSize ?>&autorun=0" class="btn btn-stop">⏸ Tạm dừng chạy tự động</a>
        <?php else: ?>
            <a href="?offset=<?= $offset ?>&batch=<?= $batchSize ?>&autorun=1" class="btn">▶️ Bắt đầu chạy tự động</a>
            <a href="?offset=<?= $offset + $batchSize ?>&batch=<?= $batchSize ?>&autorun=0" class="btn" style="background:#4b5563;">Chạy thủ công đợt tiếp theo</a>
        <?php endif; ?>
    <?php else: ?>
        <h3 class="status-ok">🎉 Chúc mừng! Đã nén xong toàn bộ <?= number_format($totalCount) ?> tệp tin ảnh!</h3>
        <p class="status-err">⚠️ Hãy xóa file này khỏi hosting ngay lập tức để bảo mật: <code>public/compress_existing_images.php</code></p>
    <?php endif; ?>
</div>

<h2>Logs đợt chạy hiện tại (Đợt: <?= $offset ?> đến <?= min($offset + $batchSize, $totalCount) ?>)</h2>
<div class="log-box">
    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Tên file</th>
                <th>Dung lượng cũ</th>
                <th>Dung lượng mới</th>
                <th>Tiết kiệm</th>
                <th>Kết quả</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $batchSaved = 0;
            $batchIndex = 1;
            
            if (empty($slicedImages)) {
                echo '<tr><td colspan="6" style="text-align:center;">Không còn ảnh nào cần xử lý.</td></tr>';
            }

            foreach ($slicedImages as $img) {
                $res = compressImage($img['path'], $img['extension'], $compressQuality);
                $relPath = str_replace($appRoot, '', $img['path']);
                
                echo '<tr>';
                echo '<td>' . ($offset + $batchIndex++) . '</td>';
                echo '<td style="font-size:11px;">' . htmlspecialchars($relPath) . '</td>';
                
                if ($res && $res['status'] === 'success') {
                    $batchSaved += $res['saved'];
                    echo '<td>' . formatBytes($res['original']) . '</td>';
                    echo '<td>' . formatBytes($res['new']) . '</td>';
                    echo '<td class="status-ok">-' . formatBytes($res['saved']) . ' (' . round(($res['saved'] / $res['original']) * 100, 1) . '%)</td>';
                    echo '<td class="status-ok">✅ Nén xong</td>';
                } elseif ($res && $res['status'] === 'skipped') {
                    echo '<td>' . formatBytes($img['size']) . '</td>';
                    echo '<td>' . formatBytes($img['size']) . '</td>';
                    echo '<td>—</td>';
                    echo '<td class="status-skip">⏭ Bỏ qua (' . $res['reason'] . ')</td>';
                } else {
                    echo '<td>' . formatBytes($img['size']) . '</td>';
                    echo '<td>' . formatBytes($img['size']) . '</td>';
                    echo '<td>—</td>';
                    echo '<td class="status-err">❌ Lỗi hoặc không thể nén</td>';
                }
                echo '</tr>';
                flush();
            }
            ?>
        </tbody>
    </table>
</div>

<?php if ($batchSaved > 0): ?>
    <h3 class="status-ok" style="margin-top: 15px;">💾 Đợt chạy này đã tiết kiệm được: <?= formatBytes($batchSaved) ?></h3>
<?php endif; ?>

<?php if ($offset < $totalCount && $autoRun): ?>
    <script>
        // Tự động chuyển trang sau 1.5 giây để tránh quá tải
        setTimeout(function() {
            window.location.href = "?offset=<?= $offset + $batchSize ?>&batch=<?= $batchSize ?>&autorun=1";
        }, 1500);
    </script>
<?php endif; ?>

</body>
</html>
