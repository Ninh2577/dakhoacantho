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
        echo json_encode($stats);
        exit;
    }

    if ($_GET['action'] === 'process_batch') {
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
                if (convertToWebP($filePath, $webpPath)) {
                    $converted++;
                    if (file_exists($webpPath) && filesize($webpPath) > 0) {
                        unlink($filePath);
                        $deleted++;
                    }
                } else {
                    $errors[] = "Failed to convert: " . $file;
                }
            } catch (\Throwable $e) {
                $errors[] = "Error processing {$file}: " . $e->getMessage();
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

// Helper: Get counts and list of unconverted files
function getOptimizerStats($dir) {
    $totalSize = 0;
    $unconvertedFiles = [];
    $totalFilesCount = 0;
    $webpCount = 0;

    if (is_dir($dir)) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isDir()) continue;
            
            $totalFilesCount++;
            $size = $file->getSize();
            $totalSize += $size;

            $ext = strtolower($file->getExtension());
            if ($ext === 'webp') {
                $webpCount++;
            } elseif (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                // Check if webp version already exists
                $webpVersion = preg_replace('/\.(jpe?g|png)$/i', '.webp', $file->getPathname());
                if (!file_exists($webpVersion)) {
                    // Store relative path
                    $unconvertedFiles[] = ltrim(str_replace($dir, '', $file->getPathname()), '/\\');
                }
            }
        }
    }

    return [
        'total_files' => $totalFilesCount,
        'total_size' => formatSize($totalSize),
        'webp_count' => $webpCount,
        'unconverted_count' => count($unconvertedFiles),
        'unconverted_files' => $unconvertedFiles
    ];
}

// Helper: Convert JPEG/PNG to WebP
function convertToWebP($source, $destination, $quality = 80) {
    if (!function_exists('imagewebp')) {
        return false;
    }

    $info = getimagesize($source);
    if (!$info) {
        return false;
    }

    $mime = $info['mime'];
    switch ($mime) {
        case 'image/jpeg':
            $image = @imagecreatefromjpeg($source);
            break;
        case 'image/png':
            $image = @imagecreatefrompng($source);
            // Preserve transparency for PNG
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
                <p class="text-sm text-slate-500">Nén WebP & dọn dẹp các thư mục rác WordPress để giải phóng dung lượng hosting.</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 gap-4" id="stats-container">
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-center">
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Tổng dung lượng</p>
                    <p class="text-xl font-extrabold text-slate-800" id="stat-size">Đang tính...</p>
                </div>
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-center">
                    <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-1">Còn lại cần nén</p>
                    <p class="text-xl font-extrabold text-sky-600" id="stat-unconverted">Đang tính...</p>
                </div>
            </div>

            <!-- Console Log -->
            <div class="bg-slate-900 rounded-2xl p-4 font-mono text-xs text-emerald-400 space-y-1 h-48 overflow-y-auto" id="console-log">
                <div class="text-slate-500">// Hệ thống đã sẵn sàng tối ưu.</div>
            </div>

            <!-- Progress Bar -->
            <div class="space-y-2 hidden" id="progress-container">
                <div class="flex justify-between text-xs font-bold text-slate-500">
                    <span>Đang tối ưu hóa hình ảnh...</span>
                    <span id="progress-percentage">0%</span>
                </div>
                <div class="w-full bg-slate-100 h-3 rounded-full overflow-hidden">
                    <div class="bg-sky-500 h-full w-0 transition-all duration-300" id="progress-bar"></div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <button type="button" id="btn-clean" class="flex-1 py-3.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold rounded-2xl border border-rose-100 transition-all">
                    1. Dọn dẹp Thư mục Rác
                </button>
                <button type="button" id="btn-start" class="flex-1 py-3.5 bg-gradient-to-r from-sky-500 to-teal-500 text-white font-extrabold rounded-2xl shadow-lg shadow-sky-500/10 hover:opacity-95 transition-opacity disabled:opacity-50" disabled>
                    2. Bắt đầu Nén WebP
                </button>
            </div>
        </div>

    </div>

    <script>
        const consoleLog = document.getElementById('console-log');
        const btnClean = document.getElementById('btn-clean');
        const btnStart = document.getElementById('btn-start');
        const progressContainer = document.getElementById('progress-container');
        const progressBar = document.getElementById('progress-bar');
        const progressPercentage = document.getElementById('progress-percentage');
        
        let stats = { unconverted_count: 0, total_unconverted: 0 };

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
                
                stats.unconverted_count = data.unconverted_count;
                if (!stats.total_unconverted) {
                    stats.total_unconverted = data.unconverted_count;
                }

                if (data.unconverted_count > 0) {
                    btnStart.disabled = false;
                } else {
                    btnStart.disabled = true;
                    log('Tất cả hình ảnh đã được chuyển đổi sang WebP!', 'success');
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

        btnStart.addEventListener('click', async () => {
            btnStart.disabled = true;
            btnClean.disabled = true;
            progressContainer.classList.remove('hidden');
            log('Bắt đầu chuyển đổi hình ảnh sang WebP đồng bộ theo lô (100 ảnh/lượt)...');
            processBatch();
        });

        async function processBatch() {
            try {
                const res = await fetch('?action=process_batch');
                const data = await res.json();

                if (data.success) {
                    log(`Đã nén và chuyển đổi thành công ${data.converted} ảnh sang WebP. Đã giải phóng file gốc.`);
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
                        // Continue to next batch
                        setTimeout(processBatch, 500);
                    } else {
                        log('Quy trình tối ưu hóa hình ảnh hoàn tất!', 'success');
                        btnStart.disabled = true;
                        btnClean.disabled = false;
                        await fetchStats();
                    }
                }
            } catch (e) {
                log('Lỗi xảy ra trong quá trình nén ảnh. Đang dừng lại.', 'error');
                btnStart.disabled = false;
                btnClean.disabled = false;
            }
        }

        // Init
        fetchStats();
    </script>
</body>
</html>
