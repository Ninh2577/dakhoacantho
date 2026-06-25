<?php

// 1. Bootstrap Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// 2. Authorization Check (Must be Logged-in Administrator)
if (!auth()->check() || auth()->user()->role !== 'admin') {
    http_response_code(403);
    die('Unauthorized. Please log in as administrator in another tab first.');
}

$uploadsPath = storage_path('app/public/uploads');

// Handle Ajax Actions
if (isset($_GET['action'])) {
    header('Content-Type: application/json; charset=utf-8');

    if ($_GET['action'] === 'clean_folders') {
        $cleaned = [];
        $foldersToClean = [
            'backwpup-060fdc-logs',
            'backwpup-060fdc-temp',
            'backwpup-restore',
            'ithemes-security',
            'tg-demo-pack'
        ];

        foreach ($foldersToClean as $folder) {
            $path = $uploadsPath . '/' . $folder;
            if (is_dir($path)) {
                deleteDirectory($path);
                $cleaned[] = $folder;
            }
        }

        // Delete .log files directly under uploads
        foreach (glob($uploadsPath . '/*.log') as $logFile) {
            unlink($logFile);
            $cleaned[] = basename($logFile);
        }

        echo json_encode(['success' => true, 'cleaned' => $cleaned]);
        exit;
    }

    if ($_GET['action'] === 'get_stats') {
        $stats = getOptimizerStats($uploadsPath);
        // Remove file lists to keep JSON lightweight
        unset($stats['unconverted_files']);
        unset($stats['thumbnail_files']);
        echo json_encode($stats);
        exit;
    }

    if ($_GET['action'] === 'process_batch') {
        $quality = isset($_GET['quality']) ? (int)$_GET['quality'] : 80;
        $resize = isset($_GET['resize']) ? (int)$_GET['resize'] : 1600;

        $stats = getOptimizerStats($uploadsPath);
        $filesToProcess = array_slice($stats['unconverted_files'], 0, 100);
        
        $processed = 0;
        $converted = 0;
        $deleted = 0;
        $errors = [];

        foreach ($filesToProcess as $file) {
            $processed++;
            $filePath = $uploadsPath . '/' . $file;
            $webpPath = preg_replace('/\.(jpe?g|png)$/i', '.webp', $filePath);

            try {
                if (convertToWebP($filePath, $webpPath, $quality, $resize)) {
                    $converted++;
                    if (file_exists($webpPath) && filesize($webpPath) > 0) {
                        unlink($filePath);
                        $deleted++;
                    }
                } else {
                    $errors[] = "Failed to convert (corrupt): " . $file;
                    @rename($filePath, $filePath . '.unsupported');
                }
            } catch (\Throwable $e) {
                $errors[] = "Error processing {$file}: " . $e->getMessage();
                @rename($filePath, $filePath . '.unsupported');
            }
        }

        echo json_encode([
            'success' => true,
            'processed' => $processed,
            'converted' => $converted,
            'deleted' => $deleted,
            'errors' => $errors,
            'remaining' => count($stats['unconverted_files']) - $processed
        ]);
        exit;
    }

    if ($_GET['action'] === 'delete_thumbnails') {
        $stats = getOptimizerStats($uploadsPath);
        $thumbnailsToDelete = array_slice($stats['thumbnail_files'], 0, 500);
        
        $deleted = 0;
        foreach ($thumbnailsToDelete as $file) {
            $filePath = $uploadsPath . '/' . $file;
            if (file_exists($filePath)) {
                if (@unlink($filePath)) {
                    $deleted++;
                }
            }
        }

        echo json_encode([
            'success' => true,
            'deleted' => $deleted,
            'remaining' => count($stats['thumbnail_files']) - count($thumbnailsToDelete)
        ]);
        exit;
    }
}

// Helper: Recursively delete directories
function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }
    if (!is_dir($dir)) {
        return @unlink($dir);
    }
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }
    return @rmdir($dir);
}

// Helper: Get counts and list of files
function getOptimizerStats($dir) {
    $totalSize = 0;
    $unconvertedFiles = [];
    $thumbnailFiles = [];
    $thumbnailSize = 0;
    $totalFilesCount = 0;
    $webpCount = 0;

    if (is_dir($dir)) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) continue;
            
            $filePath = $file->getPathname();
            $totalFilesCount++;
            $size = $file->getSize();
            $totalSize += $size;

            $ext = strtolower($file->getExtension());
            
            // Check if it is a WordPress thumbnail
            if (preg_match('/^(.+)-\d+x\d+\.(jpe?g|png|webp)$/i', $filePath, $matches)) {
                $basePath = $matches[1];
                $hasBase = false;
                foreach (['jpg', 'jpeg', 'png', 'webp'] as $bExt) {
                    if (file_exists($basePath . '.' . $bExt)) {
                        $hasBase = true;
                        break;
                    }
                }
                if ($hasBase) {
                    $thumbnailFiles[] = ltrim(str_replace($dir, '', $filePath), '/\\');
                    $thumbnailSize += $size;
                    continue;
                }
            }

            if ($ext === 'webp') {
                $webpCount++;
            } elseif (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                // Check if webp version already exists
                $webpVersion = preg_replace('/\.(jpe?g|png)$/i', '.webp', $filePath);
                if (!file_exists($webpVersion)) {
                    // Store relative path
                    $unconvertedFiles[] = ltrim(str_replace($dir, '', $filePath), '/\\');
                }
            }
        }
    }

    return [
        'total_files' => $totalFilesCount,
        'total_size' => formatSize($totalSize),
        'webp_count' => $webpCount,
        'unconverted_count' => count($unconvertedFiles),
        'unconverted_files' => $unconvertedFiles,
        'thumbnail_count' => count($thumbnailFiles),
        'thumbnail_size' => formatSize($thumbnailSize),
        'thumbnail_files' => $thumbnailFiles
    ];
}

// Helper: Convert JPEG/PNG to WebP with resize capability
function convertToWebP($source, $destination, $quality = 80, $maxDim = 1600) {
    if (!function_exists('imagewebp')) {
        return false;
    }

    $info = getimagesize($source);
    if (!$info) {
        return false;
    }

    $width = $info[0];
    $height = $info[1];
    $mime = $info['mime'];

    switch ($mime) {
        case 'image/jpeg':
            $image = @imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $image = @imagecreatefrompng($source);
            if ($image) {
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
            }
            break;
        default:
            return false;
    }

    if (!$image) {
        return false;
    }

    // Resize image if dimension is exceeded
    if ($maxDim > 0 && ($width > $maxDim || $height > $maxDim)) {
        if ($width > $height) {
            $newWidth = $maxDim;
            $newHeight = (int)($height * ($maxDim / $width));
        } else {
            $newHeight = $maxDim;
            $newWidth = (int)($width * ($maxDim / $height));
        }

        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
        if ($mime === 'image/png') {
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);
            $transparent = imagecolorallocatealpha($resizedImage, 0, 0, 0, 127);
            imagefill($resizedImage, 0, 0, $transparent);
        }

        imagecopyresampled($resizedImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($image);
        $image = $resizedImage;
    }

    $result = @imagewebp($image, $destination, $quality);
    imagedestroy($image);

    return $result;
}

function formatSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tối ưu hóa Thư mục Uploads | Phòng khám Gia Phước</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-800 flex items-center justify-center p-4">
    <div class="max-w-2xl w-full bg-white rounded-3xl border border-slate-100 shadow-xl p-8 space-y-8 relative overflow-hidden">
        
        <!-- Gradient Accents -->
        <div class="absolute -top-24 -left-24 w-48 h-48 bg-sky-200/30 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-24 -right-24 w-48 h-48 bg-teal-200/30 rounded-full blur-3xl"></div>

        <div class="relative z-10 space-y-6">
            <!-- Header -->
            <div class="text-center space-y-2">
                <div class="inline-flex p-3.5 bg-sky-50 text-sky-600 rounded-2xl border border-sky-100 mb-2">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h1 class="text-2xl font-black text-slate-900">Tối Ưu Hóa Dung Lượng Hình Ảnh</h1>
                <p class="text-sm text-slate-500">Nén WebP, dọn dẹp các thư mục rác & xóa ảnh thumbnail thừa để giải phóng dung lượng.</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-3 gap-4" id="stats-container">
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-center">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Tổng dung lượng</p>
                    <p class="text-lg font-extrabold text-slate-800" id="stat-size">Đang tính...</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-center">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Cần nén WebP</p>
                    <p class="text-lg font-extrabold text-sky-600" id="stat-unconverted">Đang tính...</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-center">
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-1">Thumbnails thừa</p>
                    <p class="text-lg font-extrabold text-rose-600" id="stat-thumbnails">Đang tính...</p>
                </div>
            </div>

            <!-- Settings Card -->
            <div class="bg-slate-50 p-5 rounded-2xl border border-slate-100 space-y-4">
                <h3 class="text-sm font-bold text-slate-700 flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Cấu hình Tối ưu & Nén ảnh
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="select-quality" class="block text-xs font-bold text-slate-500 mb-1">Chất lượng WebP (Quality)</label>
                        <select id="select-quality" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <option value="70">70 (Rất nhẹ, tối ưu nhất)</option>
                            <option value="75">75 (Cân bằng tốt)</option>
                            <option value="80" selected>80 (Mặc định, nét đẹp)</option>
                            <option value="85">85 (Chất lượng cao)</option>
                        </select>
                    </div>
                    <div>
                        <label for="select-resize" class="block text-xs font-bold text-slate-500 mb-1">Kích thước ảnh tối đa (Width/Height)</label>
                        <select id="select-resize" class="w-full bg-white border border-slate-200 rounded-xl px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-sky-500">
                            <option value="1600" selected>1600px (Khuyên dùng cho web)</option>
                            <option value="1920">1920px (Full HD)</option>
                            <option value="1200">1200px (Tiết kiệm dung lượng)</option>
                            <option value="0">Không thay đổi (Giữ nguyên kích thước gốc)</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Console Log -->
            <div class="bg-slate-900 rounded-2xl p-4 font-mono text-xs text-emerald-400 space-y-1 h-40 overflow-y-auto" id="console-log">
                <div class="text-slate-500">// Hệ thống đã sẵn sàng. Hãy chọn thao tác bên dưới.</div>
            </div>

            <!-- Progress Bar -->
            <div class="space-y-2 hidden" id="progress-container">
                <div class="flex justify-between text-xs font-bold text-slate-500">
                    <span id="progress-status">Đang tối ưu hóa...</span>
                    <span id="progress-percentage">0%</span>
                </div>
                <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden">
                    <div class="bg-sky-500 h-full w-0 transition-all duration-300" id="progress-bar"></div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col gap-3">
                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="button" id="btn-clean" class="flex-1 py-3.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold rounded-2xl border border-rose-100 transition-all text-sm">
                        1. Dọn dẹp Thư mục Rác
                    </button>
                    <button type="button" id="btn-delete-thumbnails" class="flex-1 py-3.5 bg-amber-50 hover:bg-amber-100 text-amber-700 font-bold rounded-2xl border border-amber-100 transition-all text-sm disabled:opacity-50" disabled>
                        2. Xóa Thumbnails WordPress thừa
                    </button>
                </div>
                <button type="button" id="btn-start" class="w-full py-4 bg-gradient-to-r from-sky-500 to-teal-500 text-white font-extrabold rounded-2xl shadow-lg shadow-sky-500/10 hover:opacity-95 transition-opacity disabled:opacity-50 text-base" disabled>
                    3. Bắt đầu Nén ảnh gốc sang WebP
                </button>
            </div>
        </div>

    </div>

    <script>
        const consoleLog = document.getElementById('console-log');
        const btnClean = document.getElementById('btn-clean');
        const btnDeleteThumbnails = document.getElementById('btn-delete-thumbnails');
        const btnStart = document.getElementById('btn-start');
        const progressContainer = document.getElementById('progress-container');
        const progressBar = document.getElementById('progress-bar');
        const progressPercentage = document.getElementById('progress-percentage');
        const progressStatus = document.getElementById('progress-status');
        
        const selectQuality = document.getElementById('select-quality');
        const selectResize = document.getElementById('select-resize');
        
        let stats = { unconverted_count: 0, total_unconverted: 0, thumbnail_count: 0, total_thumbnails: 0 };

        function log(message, type = 'info') {
            const div = document.createElement('div');
            if (type === 'error') {
                div.className = 'text-rose-400';
                div.innerText = '✖ ' + message;
            } else if (type === 'success') {
                div.className = 'text-teal-400';
                div.innerText = '✔ ' + message;
            } else {
                div.innerText = '> ' + message;
            }
            consoleLog.appendChild(div);
            consoleLog.scrollTop = consoleLog.scrollHeight;
        }

        async function fetchStats() {
            try {
                const res = await fetch('?action=get_stats');
                const data = await res.json();
                
                document.getElementById('stat-size').innerText = data.total_size;
                document.getElementById('stat-unconverted').innerText = data.unconverted_count + ' ảnh';
                document.getElementById('stat-thumbnails').innerText = data.thumbnail_count + ' ảnh (' + data.thumbnail_size + ')';
                
                stats.unconverted_count = data.unconverted_count;
                stats.thumbnail_count = data.thumbnail_count;

                if (data.unconverted_count > 0) {
                    btnStart.disabled = false;
                } else {
                    btnStart.disabled = true;
                    log('Tất cả hình ảnh gốc đã được nén sang WebP!', 'success');
                }

                if (data.thumbnail_count > 0) {
                    btnDeleteThumbnails.disabled = false;
                } else {
                    btnDeleteThumbnails.disabled = true;
                    log('Không có ảnh thumbnail thừa nào cần xóa.', 'success');
                }
            } catch (e) {
                log('Không thể lấy thông số thư mục uploads.', 'error');
            }
        }

        btnClean.addEventListener('click', async () => {
            btnClean.disabled = true;
            log('Đang quét và dọn dẹp các thư mục rác/log của WordPress...');
            try {
                const res = await fetch('?action=clean_folders');
                const data = await res.json();
                if (data.success && data.cleaned.length > 0) {
                    log('Đã xóa thành công: ' + data.cleaned.join(', '), 'success');
                } else {
                    log('Không có thư mục rác nào cần dọn dẹp.', 'success');
                }
                await fetchStats();
            } catch (e) {
                log('Lỗi trong quá trình dọn dẹp.', 'error');
            }
            btnClean.disabled = false;
        });

        btnDeleteThumbnails.addEventListener('click', async () => {
            if (!confirm('Bạn có chắc chắn muốn xóa tất cả ảnh WordPress thumbnail thừa? Việc này sẽ giải phóng rất nhiều dung lượng, và các ảnh này sẽ được tự động chuyển hướng hiển thị sang ảnh WebP gốc tương ứng.')) {
                return;
            }
            btnDeleteThumbnails.disabled = true;
            btnClean.disabled = true;
            btnStart.disabled = true;
            stats.total_thumbnails = stats.thumbnail_count;
            progressContainer.classList.remove('hidden');
            progressStatus.innerText = 'Đang xóa ảnh thumbnail thừa...';
            progressBar.style.width = '0%';
            progressPercentage.innerText = '0%';
            log('Bắt đầu xóa ảnh thumbnail thừa (500 ảnh/lượt)...');
            processDeleteThumbnails();
        });

        async function processDeleteThumbnails() {
            try {
                const res = await fetch('?action=delete_thumbnails');
                const data = await res.json();

                if (data.success) {
                    log(`Đã xóa thành công ${data.deleted} ảnh thumbnail thừa.`);
                    
                    const remaining = data.remaining;
                    const processedTotal = stats.total_thumbnails - remaining;
                    const percentage = Math.round((processedTotal / stats.total_thumbnails) * 100);
                    
                    progressBar.style.width = percentage + '%';
                    progressPercentage.innerText = percentage + '%';

                    document.getElementById('stat-thumbnails').innerText = remaining + ' ảnh';

                    if (remaining > 0) {
                        setTimeout(processDeleteThumbnails, 400);
                    } else {
                        log('Đã hoàn tất dọn dẹp toàn bộ ảnh thumbnail thừa!', 'success');
                        btnDeleteThumbnails.disabled = true;
                        btnClean.disabled = false;
                        await fetchStats();
                    }
                }
            } catch (e) {
                log('Lỗi xảy ra trong quá trình xóa thumbnails. Đang dừng lại.', 'error');
                btnDeleteThumbnails.disabled = false;
                btnClean.disabled = false;
                await fetchStats();
            }
        }

        btnStart.addEventListener('click', async () => {
            btnStart.disabled = true;
            btnClean.disabled = true;
            btnDeleteThumbnails.disabled = true;
            stats.total_unconverted = stats.unconverted_count;
            progressContainer.classList.remove('hidden');
            progressStatus.innerText = 'Đang nén và co kích thước ảnh gốc...';
            progressBar.style.width = '0%';
            progressPercentage.innerText = '0%';
            log(`Bắt đầu nén ảnh sang WebP (Chất lượng: ${selectQuality.value}, Max dimension: ${selectResize.value}px)...`);
            processBatch();
        });

        async function processBatch() {
            try {
                const quality = selectQuality.value;
                const resize = selectResize.value;
                const res = await fetch(`?action=process_batch&quality=${quality}&resize=${resize}`);
                const data = await res.json();

                if (data.success) {
                    log(`Đã nén & tối ưu hóa thành công ${data.converted} ảnh. Đã giải phóng file gốc.`);
                    if (data.errors && data.errors.length > 0) {
                        data.errors.forEach(err => log(err, 'error'));
                    }

                    // Update progress
                    const remaining = data.remaining;
                    const processedTotal = stats.total_unconverted - remaining;
                    const percentage = Math.round((processedTotal / stats.total_unconverted) * 100);
                    
                    progressBar.style.width = percentage + '%';
                    progressPercentage.innerText = percentage + '%';

                    document.getElementById('stat-unconverted').innerText = remaining + ' ảnh';

                    if (remaining > 0) {
                        setTimeout(processBatch, 400);
                    } else {
                        log('Quy trình tối ưu hóa và nén hình ảnh gốc hoàn tất!', 'success');
                        btnStart.disabled = true;
                        btnClean.disabled = false;
                        await fetchStats();
                    }
                }
            } catch (e) {
                log('Lỗi xảy ra trong quá trình nén ảnh. Đang dừng lại.', 'error');
                btnStart.disabled = false;
                btnClean.disabled = false;
                await fetchStats();
            }
        }

        // Init
        fetchStats();
    </script>
</body>
</html>
