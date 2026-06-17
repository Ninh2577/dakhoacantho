<?php

namespace App\Services\Security;

use App\Models\FileScanResult;
use App\Models\LoginAttempt;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class SecurityScannerService
{
    protected string $scanId;

    protected int $maxScanFileSize = 2 * 1024 * 1024; // 2MB default

    protected array $ignoredRecords = [];

    public function __construct()
    {
        $this->scanId = Str::uuid()->toString();
        $this->loadIgnoredRecords();
    }

    /**
     * Load ignored records to avoid warning about them if hash hasn't changed.
     */
    protected function loadIgnoredRecords(): void
    {
        try {
            $records = FileScanResult::where('type', FileScanResult::TYPE_IGNORED)->get();
            foreach ($records as $record) {
                $key = $record->check_key.':'.($record->target ?: $record->path);
                $this->ignoredRecords[$key] = [
                    'hash' => $record->hash,
                    'ignored_at' => $record->ignored_at,
                    'ignored_reason' => $record->ignored_reason,
                ];
            }
        } catch (\Throwable $e) {
            // Table might not exist or migration is pending
            $this->ignoredRecords = [];
        }
    }

    /**
     * Get path exclusions normalized.
     */
    public function getExcludedPaths(): array
    {
        return [
            base_path('vendor'),
            base_path('node_modules'),
            base_path('storage/framework'),
            base_path('storage/logs'),
            base_path('storage/app/public/uploads'),
            base_path('public/storage'),
            base_path('public/build'),
            base_path('bootstrap/cache'),
            storage_path('app/security'),
        ];
    }

    /**
     * Check if path is excluded.
     */
    public function isExcluded(string $path): bool
    {
        $normalizedPath = str_replace('\\', '/', $path);

        // Explicitly exclude any files under storage/app/security/ to prevent self-warnings
        if (str_contains($normalizedPath, 'storage/app/security/')) {
            return true;
        }

        $realPath = realpath($path);
        $normalizedRealPath = $realPath ? str_replace('\\', '/', $realPath) : $normalizedPath;

        foreach ($this->getExcludedPaths() as $excluded) {
            $excludedNormalized = str_replace('\\', '/', $excluded);
            $excludedReal = realpath($excluded);
            $excludedRealNormalized = $excludedReal ? str_replace('\\', '/', $excludedReal) : $excludedNormalized;

            if (str_starts_with($normalizedPath, $excludedNormalized) ||
                str_starts_with($normalizedRealPath, $excludedRealNormalized) ||
                str_starts_with($normalizedPath, $excludedRealNormalized) ||
                str_starts_with($normalizedRealPath, $excludedNormalized)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generate codebase baseline.
     */
    public function generateBaseline(): array
    {
        $baseline = [];
        $files = $this->scanCodebaseFiles();

        foreach ($files as $file) {
            if ($this->isExcluded($file)) {
                continue;
            }
            if (filesize($file) > $this->maxScanFileSize) {
                continue;
            }
            $relativePath = str_replace(base_path().DIRECTORY_SEPARATOR, '', $file);
            $baseline[$relativePath] = md5_file($file);
        }

        if (! is_dir(storage_path('app/security'))) {
            mkdir(storage_path('app/security'), 0755, true);
        }

        file_put_contents(storage_path('app/security/baseline.json'), json_encode($baseline, JSON_PRETTY_PRINT));

        return [
            'total_files' => count($baseline),
            'baseline_path' => storage_path('app/security/baseline.json'),
        ];
    }

    /**
     * Scan codebase recursively for PHP/JS files.
     */
    protected function scanCodebaseFiles(): array
    {
        $files = [];
        $dir = base_path();

        if (! is_dir($dir)) {
            return [];
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            if ($item->isFile()) {
                $ext = strtolower($item->getExtension());
                if (in_array($ext, ['php', 'js', 'html', 'htaccess'])) {
                    $files[] = $item->getPathname();
                }
            }
        }

        return $files;
    }

    /**
     * Log a scan result helper.
     */
    protected function addResult(
        string $checkKey,
        string $checkGroup,
        string $status,
        string $severity,
        string $message,
        ?string $target = null,
        ?string $recommendation = null,
        ?string $hash = null,
        ?array $meta = null
    ): void {
        $type = FileScanResult::TYPE_SUSPICIOUS;
        if ($status === 'ok') {
            $type = FileScanResult::TYPE_OK;
        }

        $ignoreKey = $checkKey.':'.($target ?: '');
        $ignoredAt = null;
        $ignoredReason = null;

        if (isset($this->ignoredRecords[$ignoreKey])) {
            $saved = $this->ignoredRecords[$ignoreKey];
            // If it's a file, only ignore if hash hasn't changed
            if (empty($hash) || $hash === $saved['hash']) {
                $type = FileScanResult::TYPE_IGNORED;
                $ignoredAt = $saved['ignored_at'];
                $ignoredReason = $saved['ignored_reason'];
            }
        }

        FileScanResult::create([
            'scan_id' => $this->scanId,
            'path' => $target ?: 'global',
            'type' => $type,
            'severity' => $severity,
            'message' => $message,
            'hash' => $hash,
            'meta' => $meta,
            'check_key' => $checkKey,
            'check_group' => $checkGroup,
            'status' => $status,
            'target' => $target,
            'recommendation' => $recommendation,
            'ignored_at' => $ignoredAt,
            'ignored_reason' => $ignoredReason,
        ]);
    }

    /**
     * Run specific check by key.
     */
    public function runCheck(string $checkKey): void
    {
        switch ($checkKey) {
            case 'spamvertising':
                $this->checkSpamvertising();
                break;
            case 'spam_check':
                $this->checkSpam();
                break;
            case 'blocklist_check':
                $this->checkBlocklist();
                break;
            case 'server_status':
                $this->checkServerStatus();
                break;
            case 'file_changes':
                $this->checkFileChanges();
                break;
            case 'malware_scan':
                $this->checkMalware();
                break;
            case 'content_safety':
                $this->checkContentSafety();
                break;
            case 'public_files':
                $this->checkPublicFiles();
                break;
            case 'password_strength':
                $this->checkPasswordStrength();
                break;
            case 'vulnerability_scan':
                $this->checkVulnerabilities();
                break;
            case 'user_check':
                $this->checkUserSecurity();
                break;
            case 'security_options':
                $this->checkSecurityOptions();
                break;
        }
    }

    /**
     * Run Quick Scan.
     */
    public function runQuickScan(): string
    {
        $quickChecks = [
            'server_status',
            'public_files',
            'password_strength',
            'security_options',
            'content_safety',
            'user_check',
            'spam_check',
            'blocklist_check',
            'vulnerability_scan',
        ];

        foreach ($quickChecks as $check) {
            $this->runCheck($check);
        }

        return $this->scanId;
    }

    /**
     * Run Full Scan.
     */
    public function runFullScan(): string
    {
        $allChecks = [
            'server_status',
            'public_files',
            'password_strength',
            'security_options',
            'content_safety',
            'user_check',
            'spam_check',
            'blocklist_check',
            'vulnerability_scan',
            'file_changes',
            'malware_scan',
            'spamvertising',
        ];

        foreach ($allChecks as $check) {
            $this->runCheck($check);
        }

        return $this->scanId;
    }

    /**
     * 1. Spamvertising Check.
     */
    protected function checkSpamvertising(): void
    {
        $files = $this->scanCodebaseFiles();
        $spamKeywords = ['viagra', 'cialis', 'levitra', 'cheap slots', 'poker online', 'payday loan'];
        $foundCount = 0;

        foreach ($files as $file) {
            if ($this->isExcluded($file)) {
                continue;
            }
            if (filesize($file) > $this->maxScanFileSize) {
                continue;
            }

            // Exclude the scanner and guidance service from keyword scan to avoid false positives
            $relativePath = str_replace(base_path().DIRECTORY_SEPARATOR, '', $file);
            $normalizedPath = str_replace('\\', '/', $relativePath);
            if (str_contains($normalizedPath, 'app/Services/Security/SecurityScannerService.php') ||
                str_contains($normalizedPath, 'app/Services/Security/SecurityFindingGuidanceService.php')
            ) {
                continue;
            }

            $handle = @fopen($file, 'r');
            if ($handle) {
                $lineNum = 0;
                $matchedKeyword = null;
                $matchedLine = '';
                $matchedLineNum = 0;

                while (($line = fgets($handle)) !== false) {
                    $lineNum++;
                    foreach ($spamKeywords as $keyword) {
                        if (stripos($line, $keyword) !== false) {
                            $matchedKeyword = $keyword;
                            $matchedLine = $line;
                            $matchedLineNum = $lineNum;
                            break 2;
                        }
                    }
                }
                fclose($handle);

                if ($matchedKeyword) {
                    $foundCount++;

                    // Create evidence metadata
                    $snippet = trim($matchedLine);
                    if (strlen($snippet) > 150) {
                        $snippet = mb_substr($snippet, 0, 147).'...';
                    }
                    $rawSnippet = htmlspecialchars($snippet, ENT_QUOTES, 'UTF-8');

                    $this->addResult(
                        'spamvertising',
                        'Spamvertising',
                        'warning',
                        FileScanResult::SEVERITY_MEDIUM,
                        "Phát hiện từ khóa nghi ngờ phát tán spam ($matchedKeyword) trong tệp.",
                        $relativePath,
                        'Kiểm tra xem mã nguồn tệp có bị chèn liên kết spam hoặc redirect ẩn đến trang web cá cược/dược phẩm hay không.',
                        md5_file($file),
                        [
                            'evidence' => [
                                'matched_pattern' => $matchedKeyword,
                                'line' => $matchedLineNum,
                                'snippet' => $rawSnippet,
                            ],
                        ]
                    );
                }
            }
        }

        if ($foundCount === 0) {
            $this->addResult(
                'spamvertising',
                'Spamvertising',
                'ok',
                FileScanResult::SEVERITY_INFO,
                'Không phát hiện dấu hiệu spamvertising trong mã nguồn.'
            );
        }
    }

    /**
     * 2. Spam Check.
     */
    protected function checkSpam(): void
    {
        // Scan article_comments database table for potential spam
        try {
            $spamKeywords = ['http://', 'https://', 'viagra', 'casino', 'slots', 'chuyển tiền', 'mua bán'];
            $query = DB::table('article_comments');

            $spamComments = [];
            foreach ($spamKeywords as $keyword) {
                $results = (clone $query)->where('content', 'like', "%{$keyword}%")->get();
                foreach ($results as $r) {
                    $spamComments[$r->id] = $r;
                }
            }

            if (count($spamComments) > 0) {
                $this->addResult(
                    'spam_check',
                    'Kiểm tra Spam',
                    'warning',
                    FileScanResult::SEVERITY_MEDIUM,
                    'Phát hiện '.count($spamComments).' bình luận có chứa liên kết hoặc từ khóa nghi vấn spam.',
                    'database:article_comments',
                    'Duyệt lại danh sách bình luận để phê duyệt hoặc xóa các bình luận rác.',
                    null,
                    ['comment_ids' => array_keys($spamComments)]
                );
            } else {
                $this->addResult(
                    'spam_check',
                    'Kiểm tra Spam',
                    'ok',
                    FileScanResult::SEVERITY_INFO,
                    'Bình luận và dữ liệu nội dung không phát hiện spam.'
                );
            }
        } catch (\Throwable $e) {
            // Table comments might not exist
            $this->addResult(
                'spam_check',
                'Kiểm tra Spam',
                'ok',
                FileScanResult::SEVERITY_INFO,
                'Không kiểm tra bình luận rác vì bảng bình luận không khả dụng.'
            );
        }
    }

    /**
     * 3. Blocklist Check.
     */
    protected function checkBlocklist(): void
    {
        // OPTIONAL & WARNING-ONLY DNSBL check
        // Resolved server IP against zen.spamhaus.org, dnsbl.sorbs.net
        $host = parse_url(config('app.url'), PHP_URL_HOST) ?: 'localhost';
        if ($host === 'localhost' || $host === '127.0.0.1') {
            $this->addResult(
                'blocklist_check',
                'Kiểm tra danh sách chặn',
                'ok',
                FileScanResult::SEVERITY_INFO,
                'Hệ thống đang chạy local (localhost/127.0.0.1). Bỏ qua kiểm tra danh sách chặn DNSBL.'
            );

            return;
        }

        try {
            $ip = gethostbyname($host);
            if (! filter_var($ip, FILTER_VALIDATE_IP)) {
                throw new \Exception("Không thể phân giải IP cho host: $host");
            }

            $reversedIp = implode('.', array_reverse(explode('.', $ip)));
            $dnsblServers = [
                'zen.spamhaus.org',
                'dnsbl.sorbs.net',
            ];

            $listed = [];
            foreach ($dnsblServers as $server) {
                $queryHost = "{$reversedIp}.{$server}";
                // Use shorter timeout by resolving DNS manually or with gethostbyname
                // In PHP on Windows, gethostbyname has system timeout, which can be long. We wrap it:
                $resolved = @gethostbyname($queryHost);
                if ($resolved !== $queryHost && str_starts_with($resolved, '127.0.0.')) {
                    $listed[] = $server;
                }
            }

            if (count($listed) > 0) {
                $this->addResult(
                    'blocklist_check',
                    'Kiểm tra danh sách chặn',
                    'warning',
                    FileScanResult::SEVERITY_LOW,
                    "IP của máy chủ ($ip) nằm trong danh sách chặn của ".implode(', ', $listed).' (Kiểm tra này mang tính chất tham khảo, không tự động chặn).',
                    $host,
                    'Yêu cầu nhà cung cấp hosting hỗ trợ gỡ IP khỏi danh sách đen hoặc kiểm tra cấu hình gửi mail của máy chủ.'
                );
            } else {
                $this->addResult(
                    'blocklist_check',
                    'Kiểm tra danh sách chặn',
                    'ok',
                    FileScanResult::SEVERITY_INFO,
                    "IP máy chủ ($ip) an toàn, không bị liệt kê trong danh sách đen DNSBL."
                );
            }
        } catch (\Throwable $e) {
            $this->addResult(
                'blocklist_check',
                'Kiểm tra danh sách chặn',
                'ok',
                FileScanResult::SEVERITY_INFO,
                'Không thể hoàn thành kiểm tra danh sách chặn: '.$e->getMessage()
            );
        }
    }

    /**
     * 4. Server Status.
     */
    protected function checkServerStatus(): void
    {
        // Check PHP version
        $phpVersion = PHP_VERSION;
        $phpOk = version_compare($phpVersion, '8.1.0', '>=');

        if (! $phpOk) {
            $this->addResult(
                'server_status',
                'Trạng thái máy chủ',
                'warning',
                FileScanResult::SEVERITY_MEDIUM,
                "Phiên bản PHP hiện tại ($phpVersion) quá cũ. Khuyến nghị PHP >= 8.1.",
                'PHP Runtime',
                'Cập nhật phiên bản PHP của máy chủ lên tối thiểu 8.1 hoặc 8.2.'
            );
        }

        // Check directory write permissions
        $writeDirs = [
            'storage' => storage_path(),
            'bootstrap/cache' => base_path('bootstrap/cache'),
        ];

        foreach ($writeDirs as $name => $path) {
            if (! is_writable($path)) {
                $this->addResult(
                    'server_status',
                    'Trạng thái máy chủ',
                    'warning',
                    FileScanResult::SEVERITY_HIGH,
                    "Thư mục $name không thể ghi dữ liệu bởi PHP.",
                    $name,
                    "Thay đổi quyền truy cập (chmod) thư mục $name thành có thể ghi bởi PHP (thường là 775 hoặc 755)."
                );
            }
        }

        // Check debug mode
        $debug = config('app.debug');
        $env = config('app.env');

        if ($debug) {
            $severity = ($env === 'production' || $env === 'staging') ? FileScanResult::SEVERITY_CRITICAL : FileScanResult::SEVERITY_INFO;
            $status = ($env === 'production' || $env === 'staging') ? 'warning' : 'ok';

            $this->addResult(
                'server_status',
                'Trạng thái máy chủ',
                $status,
                $severity,
                "Chế độ gỡ lỗi (APP_DEBUG=true) đang bật ở môi trường [$env].",
                'APP_DEBUG',
                'Tắt APP_DEBUG trong tệp tin .env (APP_DEBUG=false) để tránh lộ thông tin cấu hình hệ thống khi xảy ra lỗi.'
            );
        } else {
            $this->addResult(
                'server_status',
                'Trạng thái máy chủ',
                'ok',
                FileScanResult::SEVERITY_INFO,
                'Trạng thái máy chủ bình thường. Phiên bản PHP và phân quyền thư mục đảm bảo.'
            );
        }
    }

    /**
     * 5. File Changes.
     */
    protected function checkFileChanges(): void
    {
        $baselinePath = storage_path('app/security/baseline.json');
        if (! file_exists($baselinePath)) {
            $this->addResult(
                'file_changes',
                'Thay đổi tệp',
                'warning',
                FileScanResult::SEVERITY_LOW,
                'Chưa khởi tạo Baseline quét tệp. Hãy nhấn nút "Tạo baseline" để lưu trạng thái hiện tại.',
                'baseline.json',
                'Tạo baseline để bắt đầu so sánh thay đổi tệp tin mã nguồn.'
            );

            return;
        }

        $baseline = json_decode(file_get_contents($baselinePath), true) ?: [];
        $files = $this->scanCodebaseFiles();
        $current = [];
        $changedCount = 0;

        foreach ($files as $file) {
            if ($this->isExcluded($file)) {
                continue;
            }
            if (filesize($file) > $this->maxScanFileSize) {
                continue;
            }
            $relativePath = str_replace(base_path().DIRECTORY_SEPARATOR, '', $file);
            $current[$relativePath] = md5_file($file);
        }

        // Detect modified or new files
        foreach ($current as $path => $hash) {
            if (! isset($baseline[$path])) {
                $changedCount++;
                $this->addResult(
                    'file_changes',
                    'Thay đổi tệp',
                    'warning',
                    FileScanResult::SEVERITY_MEDIUM,
                    'Phát hiện tệp tin mới không nằm trong baseline.',
                    $path,
                    'Hãy kiểm tra xem tệp tin mới này có phải là tệp tin hợp pháp hay mã độc vừa bị đẩy lên.',
                    $hash
                );
            } elseif ($baseline[$path] !== $hash) {
                $changedCount++;
                $this->addResult(
                    'file_changes',
                    'Thay đổi tệp',
                    'warning',
                    FileScanResult::SEVERITY_HIGH,
                    'Phát hiện tệp tin bị thay đổi nội dung (sai lệch mã MD5 so với baseline).',
                    $path,
                    'Kiểm tra lịch sử git hoặc các thay đổi thủ công gần đây của tệp tin.',
                    $hash
                );
            }
        }

        // Detect deleted files
        foreach ($baseline as $path => $hash) {
            if (! isset($current[$path]) && ! file_exists(base_path($path))) {
                $changedCount++;
                $this->addResult(
                    'file_changes',
                    'Thay đổi tệp',
                    'warning',
                    FileScanResult::SEVERITY_MEDIUM,
                    'Phát hiện tệp tin trong baseline đã bị xóa khỏi hệ thống.',
                    $path,
                    'Khôi phục tệp tin nếu việc xóa này là ngoài ý muốn.'
                );
            }
        }

        if ($changedCount === 0) {
            $this->addResult(
                'file_changes',
                'Thay đổi tệp',
                'ok',
                FileScanResult::SEVERITY_INFO,
                'Không phát hiện thay đổi tệp tin nào so với Baseline.'
            );
        }
    }

    /**
     * 6. Malware Scan.
     */
    protected function checkMalware(): void
    {
        $files = $this->scanCodebaseFiles();
        $patterns = [
            '/(?<![a-zA-Z0-9_])eval\s*\(/i' => 'Sử dụng eval() thực thi code động',
            '/(?<![a-zA-Z0-9_])shell_exec\s*\(/i' => 'Sử dụng shell_exec() thực thi lệnh shell',
            '/(?<![a-zA-Z0-9_])system\s*\(/i' => 'Sử dụng system() thực thi lệnh shell',
            '/(?<![a-zA-Z0-9_])passthru\s*\(/i' => 'Sử dụng passthru() thực thi lệnh shell',
            '/(?<![a-zA-Z0-9_])exec\s*\(/i' => 'Sử dụng exec() thực thi lệnh shell',
            '/(?<![a-zA-Z0-9_])base64_decode\s*\(/i' => 'Sử dụng base64_decode()',
        ];

        $foundMalware = 0;

        // Check if there are any PHP files in uploads folder
        $uploadsDir = public_path('storage/uploads');
        if (is_dir($uploadsDir)) {
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($uploadsDir, RecursiveDirectoryIterator::SKIP_DOTS)
            );
            foreach ($iterator as $item) {
                if ($item->isFile() && strtolower($item->getExtension()) === 'php') {
                    $foundMalware++;
                    $relativePath = str_replace(base_path().DIRECTORY_SEPARATOR, '', $item->getPathname());

                    $this->addResult(
                        'malware_scan',
                        'Quét mã độc',
                        'warning',
                        FileScanResult::SEVERITY_CRITICAL,
                        'Phát hiện tệp PHP nằm trong thư mục uploads công khai.',
                        $relativePath,
                        'Hệ thống WordPress/Laravel tuyệt đối không cho phép chạy file PHP trong mục tải lên. Hãy kiểm tra và xóa tệp tin này ngay lập tức.',
                        md5_file($item->getPathname()),
                        [
                            'evidence' => [
                                'matched_pattern' => 'php_in_uploads',
                                'line' => 1,
                                'snippet' => '[Tệp PHP bị cấm chạy trực tiếp từ thư mục tải lên]',
                            ],
                        ]
                    );
                }
            }
        }

        // Check public directory for env files
        if (file_exists(public_path('.env'))) {
            $foundMalware++;
            $this->addResult(
                'malware_scan',
                'Quét mã độc',
                'warning',
                FileScanResult::SEVERITY_CRITICAL,
                'Tệp .env cấu hình hệ thống nằm công khai trong thư mục public.',
                'public/.env',
                'Di chuyển tệp .env ra khỏi thư mục public ngay lập tức.',
                md5_file(public_path('.env')),
                [
                    'evidence' => [
                        'matched_pattern' => 'public_env_exposed',
                        'line' => 1,
                        'snippet' => '[Đã ẩn nội dung nhạy cảm để bảo mật]',
                    ],
                ]
            );
        }

        // Scan code for constructs
        foreach ($files as $file) {
            if ($this->isExcluded($file)) {
                continue;
            }
            if (filesize($file) > $this->maxScanFileSize) {
                continue;
            }

            $handle = @fopen($file, 'r');
            if ($handle) {
                $lineNum = 0;
                $matchedPattern = null;
                $matchedLine = '';
                $matchedLineNum = 0;
                $matchedDescription = '';

                while (($line = fgets($handle)) !== false) {
                    $lineNum++;
                    foreach ($patterns as $regex => $description) {
                        if (preg_match($regex, $line)) {
                            $matchedPattern = $regex;
                            $matchedLine = $line;
                            $matchedLineNum = $lineNum;
                            $matchedDescription = $description;
                            break 2;
                        }
                    }
                }
                fclose($handle);

                if ($matchedPattern) {
                    $foundMalware++;
                    $relativePath = str_replace(base_path().DIRECTORY_SEPARATOR, '', $file);

                    // Determine base severity
                    $severity = FileScanResult::SEVERITY_HIGH;
                    if ($matchedDescription === 'Sử dụng base64_decode()') {
                        $severity = FileScanResult::SEVERITY_MEDIUM;
                    }
                    if (str_contains($file, 'public'.DIRECTORY_SEPARATOR)) {
                        $severity = FileScanResult::SEVERITY_CRITICAL;
                    }

                    // Check if file is in allowlist, down-grade severity to low (still warnings for audit review)
                    $isAllowlisted = false;
                    $normalizedRel = str_replace('\\', '/', $relativePath);
                    $allowlist = [
                        'app/Services/Security/SecurityScannerService.php',
                        'app/Services/Security/SecurityFindingGuidanceService.php',
                        'app/Console/Commands/ImportOldArticles.php',
                        'app/Console/Commands/RemapArticleCategories.php',
                        'app/Services/WordPress/WordPressImportService.php',
                        'app/Jobs/RecompileUrlPathsJob.php',
                    ];
                    foreach ($allowlist as $al) {
                        if (str_contains($normalizedRel, $al)) {
                            $isAllowlisted = true;
                            break;
                        }
                    }

                    if ($isAllowlisted) {
                        $severity = FileScanResult::SEVERITY_LOW;
                    }

                    // Format snippet
                    $snippet = trim($matchedLine);
                    if (strlen($snippet) > 150) {
                        $snippet = mb_substr($snippet, 0, 147).'...';
                    }

                    // Hide snippets for sensitive files
                    if (str_contains($normalizedRel, '.env') || str_contains($normalizedRel, 'config/')) {
                        $rawSnippet = '[Đã ẩn nội dung nhạy cảm để bảo mật]';
                    } else {
                        $rawSnippet = htmlspecialchars($snippet, ENT_QUOTES, 'UTF-8');
                    }

                    $this->addResult(
                        'malware_scan',
                        'Quét mã độc',
                        'warning',
                        $severity,
                        "Phát hiện mẫu lệnh nguy hiểm ($matchedDescription) trong mã nguồn.",
                        $relativePath,
                        'Kiểm tra xem tệp tin có sử dụng các hàm này một cách an toàn không, hoặc có dấu hiệu bị tiêm mã độc.',
                        md5_file($file),
                        [
                            'evidence' => [
                                'matched_pattern' => $matchedDescription,
                                'line' => $matchedLineNum,
                                'snippet' => $rawSnippet,
                            ],
                        ]
                    );
                }
            }
        }

        if ($foundMalware === 0) {
            $this->addResult(
                'malware_scan',
                'Quét mã độc',
                'ok',
                FileScanResult::SEVERITY_INFO,
                'Không phát hiện tệp PHP nguy hiểm hay tệp cấu hình lộ diện trong thư mục tải lên.'
            );
        }
    }

    /**
     * 7. Content Safety.
     */
    protected function checkContentSafety(): void
    {
        try {
            $spamKeywords = ['<script', 'javascript:', 'onerror=', 'onload=', 'iframe', 'window.location'];
            $articles = DB::table('articles')->get();
            $settings = DB::table('settings')->get();
            $foundCount = 0;

            foreach ($articles as $art) {
                foreach ($spamKeywords as $kw) {
                    if (str_contains($art->body ?? '', $kw) || str_contains($art->summary ?? '', $kw)) {
                        $foundCount++;

                        $text = str_contains($art->body ?? '', $kw) ? ($art->body ?? '') : ($art->summary ?? '');
                        $rawSnippet = $this->sanitizeDatabaseSnippet($text, $kw);

                        $this->addResult(
                            'content_safety',
                            'An toàn nội dung',
                            'warning',
                            FileScanResult::SEVERITY_HIGH,
                            "Phát hiện thẻ script hoặc mã script tiêm nhiễm trong nội dung bài viết ID [{$art->id}] (Tiêu đề: {$art->title}).",
                            "database:articles:{$art->id}",
                            'Chỉnh sửa bài viết để loại bỏ các đoạn mã script đáng ngờ, tránh lỗi tấn công XSS chéo trang.',
                            null,
                            [
                                'evidence' => [
                                    'article_id' => $art->id,
                                    'title' => $art->title,
                                    'field' => str_contains($art->body ?? '', $kw) ? 'body' : 'summary',
                                    'matched_pattern' => $kw,
                                    'snippet' => $rawSnippet,
                                    'admin_edit_url' => "/admin/articles/{$art->id}/edit",
                                ],
                            ]
                        );
                        break;
                    }
                }
            }

            foreach ($settings as $setting) {
                foreach ($spamKeywords as $kw) {
                    if (str_contains($setting->value ?? '', $kw)) {
                        $foundCount++;

                        $text = $setting->value ?? '';
                        $rawSnippet = $this->sanitizeDatabaseSnippet($text, $kw);

                        $this->addResult(
                            'content_safety',
                            'An toàn nội dung',
                            'warning',
                            FileScanResult::SEVERITY_CRITICAL,
                            "Phát hiện mã script trong cài đặt hệ thống key [{$setting->key}].",
                            "database:settings:{$setting->key}",
                            'Kiểm tra cấu hình hệ thống xem có bị chèn mã độc quảng cáo hoặc mã độc thu thập cookie.',
                            null,
                            [
                                'evidence' => [
                                    'field' => 'value',
                                    'matched_pattern' => $kw,
                                    'snippet' => $rawSnippet,
                                    'admin_edit_url' => '/admin/home-page-settings',
                                ],
                            ]
                        );
                        break;
                    }
                }
            }

            if ($foundCount === 0) {
                $this->addResult(
                    'content_safety',
                    'An toàn nội dung',
                    'ok',
                    FileScanResult::SEVERITY_INFO,
                    'Toàn bộ bài viết và cài đặt hệ thống an toàn, không chứa mã độc XSS.'
                );
            }
        } catch (\Throwable $e) {
            $this->addResult(
                'content_safety',
                'An toàn nội dung',
                'ok',
                FileScanResult::SEVERITY_INFO,
                'Bỏ qua quét nội dung an toàn vì bảng bài viết/cài đặt không khả dụng.'
            );
        }
    }

    /**
     * 8. Public Files.
     */
    protected function checkPublicFiles(): void
    {
        $sensitiveFiles = [
            '.env',
            '.git/config',
            'composer.json',
            'composer.lock',
            'package.json',
            'phpinfo.php',
            'test.php',
            'backup.sql',
            'db.sql',
            'data.zip',
            'backup.zip',
        ];

        $exposedFiles = [];

        foreach ($sensitiveFiles as $file) {
            $filePath = public_path($file);
            // Local check is safe and fast
            if (file_exists($filePath)) {
                $exposedFiles[] = $file;
                $this->addResult(
                    'public_files',
                    'Tệp công khai',
                    'warning',
                    FileScanResult::SEVERITY_HIGH,
                    "Phát hiện tệp nhạy cảm ($file) nằm trong thư mục public và có thể truy cập được.",
                    "public/$file",
                    'Di chuyển tệp tin này ra ngoài thư mục public hoặc cấu hình webserver (.htaccess / nginx config) để chặn truy cập trực tiếp từ trình duyệt.'
                );
            }
        }

        if (count($exposedFiles) === 0) {
            $this->addResult(
                'public_files',
                'Tệp công khai',
                'ok',
                FileScanResult::SEVERITY_INFO,
                'Không phát hiện tệp tin cấu hình hoặc tệp tin nhạy cảm công khai trong thư mục public.'
            );
        }
    }

    /**
     * 9. Password Strength.
     */
    protected function checkPasswordStrength(): void
    {
        try {
            $users = User::all();
            $issueCount = 0;

            foreach ($users as $user) {
                // Check for invalid or empty hashes
                if (empty($user->password) || strlen($user->password) < 20) {
                    $issueCount++;
                    $this->addResult(
                        'password_strength',
                        'Độ mạnh của mật khẩu',
                        'warning',
                        FileScanResult::SEVERITY_CRITICAL,
                        "Người dùng ID [{$user->id}] ({$user->email}) có mã băm mật khẩu rỗng hoặc không hợp lệ.",
                        "user:{$user->id}",
                        'Cập nhật mật khẩu hợp lệ cho tài khoản này ngay lập tức.'
                    );
                }

                // Check for default admin@dakhoacantho.com with default password "password"
                if ($user->email === 'admin@dakhoacantho.com') {
                    if (Hash::check('password', $user->password)) {
                        $issueCount++;
                        $this->addResult(
                            'password_strength',
                            'Độ mạnh của mật khẩu',
                            'warning',
                            FileScanResult::SEVERITY_CRITICAL,
                            "Tài khoản quản trị mặc định (admin@dakhoacantho.com) đang sử dụng mật khẩu mặc định 'password'.",
                            "user:{$user->id}",
                            'Thay đổi mật khẩu tài khoản quản trị sang một mật khẩu mạnh hơn để tránh bị chiếm quyền điều khiển.'
                        );
                    }
                }
            }

            // Check failed logins history for alerts
            $failedCountToday = LoginAttempt::where('successful', false)
                ->whereDate('created_at', today())
                ->count();

            if ($failedCountToday >= 20) {
                $issueCount++;
                $this->addResult(
                    'password_strength',
                    'Độ mạnh của mật khẩu',
                    'warning',
                    FileScanResult::SEVERITY_HIGH,
                    "Phát hiện tần suất đăng nhập thất bại rất cao ($failedCountToday lần) trong ngày hôm nay.",
                    'login_attempts',
                    'Hệ thống có thể đang bị tấn công dò mật khẩu (brute-force). Hãy kiểm tra IP và cân nhắc kích hoạt chính sách chặn IP tự động.'
                );
            }

            if ($issueCount === 0) {
                $this->addResult(
                    'password_strength',
                    'Độ mạnh của mật khẩu',
                    'ok',
                    FileScanResult::SEVERITY_INFO,
                    'Mật khẩu người dùng hợp lệ, không phát hiện tài khoản sử dụng mật khẩu mặc định.'
                );
            }
        } catch (\Throwable $e) {
            $this->addResult(
                'password_strength',
                'Độ mạnh của mật khẩu',
                'ok',
                FileScanResult::SEVERITY_INFO,
                'Bỏ qua kiểm tra mật khẩu vì bảng người dùng không khả dụng.'
            );
        }
    }

    /**
     * 10. Vulnerability Scan.
     */
    protected function checkVulnerabilities(): void
    {
        // Run: rtk composer audit
        // Safe check if command can be run
        if (! function_exists('shell_exec')) {
            $this->addResult(
                'vulnerability_scan',
                'Quét lỗ hổng bảo mật',
                'warning',
                FileScanResult::SEVERITY_LOW,
                'Hàm shell_exec bị vô hiệu hóa trên máy chủ. Không thể tự động chạy "composer audit".',
                'composer audit',
                'Hãy kiểm tra thủ công các lỗ hổng bảo mật của gói thư viện bằng lệnh: composer audit trên dòng lệnh.'
            );

            return;
        }

        try {
            // Run composer audit using the project paths
            $output = @shell_exec('composer audit --format=json 2>&1');
            if (empty($output) || str_contains($output, 'not found') || str_contains($output, 'is not recognized')) {
                // If composer command not found, try using direct composer.phar or fallback to raw message
                if (file_exists(base_path('composer.phar'))) {
                    $output = @shell_exec('php '.base_path('composer.phar').' audit --format=json 2>&1');
                }
            }

            if (empty($output)) {
                throw new \Exception('Không nhận được phản hồi từ Composer CLI.');
            }

            // Parse json if output is json
            $data = json_decode($output, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                // Not json, check if text indicates no vulnerabilities
                if (str_contains($output, 'No security vulnerability advisories found')) {
                    $this->addResult(
                        'vulnerability_scan',
                        'Quét lỗ hổng bảo mật',
                        'ok',
                        FileScanResult::SEVERITY_INFO,
                        'composer audit: Không tìm thấy lỗ hổng bảo mật nào trong các gói thư viện.'
                    );

                    return;
                }

                // Text indicates error or vulnerabilities
                throw new \Exception('Lỗi phản hồi Composer: '.substr(strip_tags($output), 0, 100));
            }

            $vulnCount = 0;
            if (isset($data['advisories']) && is_array($data['advisories'])) {
                foreach ($data['advisories'] as $pkg => $advisories) {
                    foreach ($advisories as $adv) {
                        $vulnCount++;
                        $this->addResult(
                            'vulnerability_scan',
                            'Quét lỗ hổng bảo mật',
                            'warning',
                            FileScanResult::SEVERITY_HIGH,
                            "Thư viện [{$pkg}] có lỗ hổng bảo mật: {$adv['title']} (Phiên bản cài đặt: {$adv['affectedVersions']})",
                            "composer:{$pkg}",
                            "Chạy lệnh: composer update {$pkg} để nâng cấp thư viện lên phiên bản an toàn hơn."
                        );
                    }
                }
            }

            if ($vulnCount === 0) {
                $this->addResult(
                    'vulnerability_scan',
                    'Quét lỗ hổng bảo mật',
                    'ok',
                    FileScanResult::SEVERITY_INFO,
                    'Không phát hiện lỗ hổng bảo mật nào từ composer audit.'
                );
            }
        } catch (\Throwable $e) {
            $this->addResult(
                'vulnerability_scan',
                'Quét lỗ hổng bảo mật',
                'warning',
                FileScanResult::SEVERITY_LOW,
                'Không thể hoàn thành tự động quét lỗ hổng Composer: '.$e->getMessage().'. Trạng thái: Not Checked.',
                'composer audit',
                'Hãy chạy lệnh "rtk composer audit" thủ công trên máy chủ để xác minh lỗ hổng.'
            );
        }
    }

    /**
     * 11. User Check.
     */
    protected function checkUserSecurity(): void
    {
        try {
            // Count admin users
            $admins = User::where('role', 'admin')->get();
            $issueCount = 0;

            if ($admins->count() > 5) {
                $issueCount++;
                $this->addResult(
                    'user_check',
                    'Kiểm tra người dùng',
                    'warning',
                    FileScanResult::SEVERITY_MEDIUM,
                    "Phát hiện số lượng tài khoản Admin khá lớn ({$admins->count()} tài khoản).",
                    'users:admins',
                    'Rà soát lại danh sách quản trị viên để thu hồi quyền của các tài khoản không cần thiết.'
                );
            }

            // Check for duplicate emails
            $duplicates = User::select('email', DB::raw('COUNT(*) as count'))
                ->groupBy('email')
                ->having('count', '>', 1)
                ->get();

            if ($duplicates->count() > 0) {
                $issueCount++;
                $this->addResult(
                    'user_check',
                    'Kiểm tra người dùng',
                    'warning',
                    FileScanResult::SEVERITY_HIGH,
                    'Phát hiện '.$duplicates->count().' email bị trùng lặp trong bảng người dùng.',
                    'users:emails',
                    'Xử lý và dọn dẹp các tài khoản có email trùng lặp để tránh xung đột hệ thống đăng nhập.'
                );
            }

            if ($issueCount === 0) {
                $this->addResult(
                    'user_check',
                    'Kiểm tra người dùng',
                    'ok',
                    FileScanResult::SEVERITY_INFO,
                    'Cấu trúc tài khoản quản trị bình thường, không có email trùng lặp.'
                );
            }
        } catch (\Throwable $e) {
            $this->addResult(
                'user_check',
                'Kiểm tra người dùng',
                'ok',
                FileScanResult::SEVERITY_INFO,
                'Bỏ qua kiểm tra người dùng vì bảng người dùng không khả dụng.'
            );
        }
    }

    /**
     * 12. Security Options.
     */
    protected function checkSecurityOptions(): void
    {
        $issueCount = 0;

        // Check HTTPS
        $url = config('app.url');
        if (! str_starts_with(strtolower($url), 'https://')) {
            $issueCount++;
            $this->addResult(
                'security_options',
                'Tùy chọn bảo mật',
                'warning',
                FileScanResult::SEVERITY_HIGH,
                "Cấu hình APP_URL trong tệp .env không sử dụng giao thức bảo mật HTTPS ($url).",
                'APP_URL',
                'Thay đổi APP_URL thành giao thức HTTPS và cài đặt chứng chỉ SSL cho tên miền.'
            );
        }

        // Check Session Cookie configuration
        $secureCookie = config('session.secure');
        if (! $secureCookie) {
            $issueCount++;
            $this->addResult(
                'security_options',
                'Tùy chọn bảo mật',
                'warning',
                FileScanResult::SEVERITY_MEDIUM,
                'Thuộc tính Secure của cookie phiên làm việc (SESSION_SECURE_COOKIE) chưa được kích hoạt.',
                'session.secure',
                'Đặt SESSION_SECURE_COOKIE=true trong tệp .env khi chạy trên môi trường HTTPS để bảo vệ phiên đăng nhập khỏi bị đánh cắp.'
            );
        }

        if ($issueCount === 0) {
            $this->addResult(
                'security_options',
                'Tùy chọn bảo mật',
                'ok',
                FileScanResult::SEVERITY_INFO,
                'Các tùy chọn cấu hình HTTPS và bảo mật cookie được thiết lập an toàn.'
            );
        }
    }

    /**
     * Get latest scan summary.
     */
    public function getLatestSummary(): array
    {
        $latestScan = FileScanResult::orderBy('created_at', 'desc')->first();
        if (! $latestScan) {
            return [
                'has_scan' => false,
                'scan_id' => null,
                'scanned_at' => null,
                'total_results' => 0,
                'total_threats' => 0,
                'total_ok' => 0,
                'status' => 'no_scan',
            ];
        }

        $scanId = $latestScan->scan_id;
        $results = FileScanResult::byScan($scanId)->get();

        $totalThreats = $results->where('type', '!=', FileScanResult::TYPE_OK)
            ->where('type', '!=', FileScanResult::TYPE_IGNORED)
            ->where('type', '!=', FileScanResult::TYPE_REVIEWED)
            ->count();

        $totalOk = $results->where('type', FileScanResult::TYPE_OK)->count();
        $totalIgnored = $results->where('type', FileScanResult::TYPE_IGNORED)->count();

        $status = 'healthy';
        if ($totalThreats > 0) {
            $critical = $results->where('severity', FileScanResult::SEVERITY_CRITICAL)
                ->where('type', '!=', FileScanResult::TYPE_IGNORED)
                ->count();
            $high = $results->where('severity', FileScanResult::SEVERITY_HIGH)
                ->where('type', '!=', FileScanResult::TYPE_IGNORED)
                ->count();

            if ($critical > 0 || $high > 0) {
                $status = 'danger';
            } else {
                $status = 'warning';
            }
        }

        return [
            'has_scan' => true,
            'scan_id' => $scanId,
            'scanned_at' => $latestScan->created_at->format('d/m/Y H:i:s'),
            'total_results' => $results->count(),
            'total_threats' => $totalThreats,
            'total_ok' => $totalOk,
            'total_ignored' => $totalIgnored,
            'status' => $status,
        ];
    }

    /**
     * Sanitize database snippet to avoid exposing sensitive medical data.
     * Keeps code keywords, masks other words, and HTML escapes.
     */
    protected function sanitizeDatabaseSnippet(string $text, string $keyword): string
    {
        $pos = stripos($text, $keyword);
        if ($pos === false) {
            return '';
        }

        $start = max(0, $pos - 45);
        $snippet = mb_substr($text, $start, 100);

        // Truncate markers
        $prefix = ($start > 0) ? '...' : '';
        $suffix = ($pos + strlen($keyword) + 55 < strlen($text)) ? '...' : '';
        $snippet = $prefix.$snippet.$suffix;

        // Strip HTML tags to avoid rendering raw HTML in the snippet container, but keep text
        $snippet = strip_tags($snippet);

        // Mask any words to protect sensitive medical/patient data
        // Matches any sequence of letters/numbers (including Vietnamese Unicode)
        $allowedCodeWords = [
            'script', 'iframe', 'onerror', 'onload', 'javascript', 'window', 'location',
            'var', 'let', 'const', 'function', 'http', 'https', 'src', 'href', 'width',
            'height', 'style', 'document', 'cookie', 'alert', 'eval', 'onmouseover',
            'body', 'summary', 'settings', 'value', 'key',
        ];

        $snippet = preg_replace_callback('/[\p{L}\p{N}_]+/u', function ($matches) use ($allowedCodeWords, $keyword) {
            $word = $matches[0];
            $lowerWord = strtolower($word);

            // Keep allowed code keywords or words that are part of the target match keyword
            if (stripos($keyword, $word) !== false || in_array($lowerWord, $allowedCodeWords)) {
                return $word;
            }

            // Mask sensitive patient/medical words with *
            return str_repeat('*', min(strlen($word), 5));
        }, $snippet);

        // HTML escape to be safe
        return htmlspecialchars($snippet, ENT_QUOTES, 'UTF-8');
    }
}
