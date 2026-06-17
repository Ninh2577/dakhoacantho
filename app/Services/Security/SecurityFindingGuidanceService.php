<?php

namespace App\Services\Security;

use App\Models\FileScanResult;

class SecurityFindingGuidanceService
{
    /**
     * Build structured guidance for a scan finding.
     */
    public function build(FileScanResult $finding): array
    {
        $checkKey = $finding->check_key;
        $meta = $finding->meta ?: [];
        $evidence = $meta['evidence'] ?? [];

        // Base fields
        $summary = $finding->message;
        $whyFlagged = 'Hạng mục này được quét nhằm kiểm tra tính an toàn tổng thể của hệ thống.';
        $impact = 'Mức độ ảnh hưởng phụ thuộc vào loại lỗ hổng.';
        $confidence = 'medium'; // Default: medium (Trung bình)
        $falsePositiveHint = 'Chưa có thông tin xác định cảnh báo giả cho hạng mục này.';

        $manualChecks = [
            'Kiểm tra lại tệp tin hoặc dữ liệu tương ứng được báo cáo trong mục tiêu.',
            'Xem xét lịch sử thay đổi (git log) nếu có.',
        ];

        $remediationSteps = [
            'Sao lưu (backup) lại tệp tin hoặc cơ sở dữ liệu trước khi thực hiện bất kỳ chỉnh sửa nào.',
            'Thử nghiệm thay đổi trên môi trường thử nghiệm (local/staging) trước khi áp dụng trên môi trường thực tế (production).',
            'Chạy lại quét bảo mật nhanh/đầy đủ để kiểm tra xem cảnh báo đã biến mất chưa.',
        ];

        $nextActions = [
            'Nếu xác nhận đây là cảnh báo giả hoặc hành vi hợp lệ, hãy nhấn nút "Bỏ qua" hoặc "Đã duyệt" để ẩn cảnh báo này.',
            'Nếu phát hiện mã độc thực sự, hãy tiến hành làm sạch mã nguồn từ bản backup sạch hoặc liên hệ kỹ thuật viên bảo mật.',
        ];

        // Specific rules per check key
        switch ($checkKey) {
            case 'spamvertising':
                $whyFlagged = 'Cảnh báo này xuất hiện do hệ thống phát hiện các từ khóa liên quan đến quảng cáo rác (như casino, cá cược, viagra, pharmacy) trong mã nguồn tệp tin. Kẻ tấn công thường chèn những từ này để làm SEO mũ đen (Spam SEO) nhằm kiếm lợi bất chính từ lưu lượng truy cập của bạn.';
                $impact = 'Nguy hiểm trung bình (MEDIUM). Website có thể bị Google đánh tụt thứ hạng tìm kiếm hoặc bị chặn hiển thị nếu phát tán spam SEO.';

                $isScannerFile = str_contains($finding->path, 'SecurityScannerService.php') || str_contains($finding->path, 'SecurityFindingGuidanceService.php');
                if ($isScannerFile) {
                    $confidence = 'low';
                    $falsePositiveHint = 'Có khả năng cao đây là cảnh báo giả. Tệp tin này thuộc module quét bảo mật và chứa danh sách các từ khóa nhằm phục vụ mục đích quét hoặc hiển thị hướng dẫn. Do đó, việc tệp chứa từ khóa "viagra" là hoàn toàn bình thường.';

                    array_unshift($manualChecks, 'Xác nhận xem đoạn chứa từ khóa "viagra" có thực sự nằm trong khối mã định nghĩa hợp lệ hay bị chèn lạ.');
                    array_unshift($remediationSteps, 'Không cần thay đổi mã nguồn nếu từ khóa nằm trong khối mã hợp lệ của module quét bảo mật.');
                    array_unshift($nextActions, 'Đánh dấu "Bỏ qua" cảnh báo giả này để hệ thống không cảnh báo lại ở các phiên quét sau.');
                } else {
                    $confidence = 'high';
                    $falsePositiveHint = 'Nếu đây là một bài viết y tế hợp lệ hoặc trang web có nội dung giới thiệu sản phẩm y khoa chính thống, đây có thể là hành vi bình thường. Tuy nhiên, nếu đây là tệp tin PHP hoặc tệp tin mã nguồn thông thường, khả năng cao là tệp đã bị hacker sửa đổi.';

                    array_unshift($manualChecks, 'Mở tệp tin theo đường dẫn được báo cáo.', 'Kiểm tra xem từ khóa được tìm thấy có nằm trong khối liên kết ẩn (ví dụ: thẻ a có style display:none) hoặc lệnh chuyển hướng (redirect) hay không.');
                    array_unshift($remediationSteps, 'Tải phiên bản tệp sạch từ Git hoặc mã nguồn gốc để đè lên tệp bị sửa đổi.', 'Nếu tệp được tạo mới hoàn toàn mà không có lý do, hãy xóa tệp sau khi đã sao lưu và xác nhận nó không ảnh hưởng đến hoạt động của hệ thống.');
                }
                break;

            case 'malware_scan':
                $whyFlagged = 'Mã độc thường sử dụng các hàm thực thi mã động (như eval, shell_exec, system, exec, passthru) hoặc mã hóa base64 để che giấu hoạt động của chúng khỏi các công cụ quét bảo mật thông thường.';
                $impact = 'Nguy hiểm cao đến cực kỳ nghiêm trọng (HIGH/CRITICAL). Các hàm này cho phép thực thi lệnh từ xa trên máy chủ, có thể dẫn đến việc mất toàn bộ quyền kiểm soát website.';

                $isAllowlisted = $this->isAllowlistedSystemFile($finding->path);

                if ($isAllowlisted) {
                    $confidence = 'low';
                    $falsePositiveHint = 'Cảnh báo giả khả nghi. Các tệp dịch vụ hoặc console commands hệ thống như scanner, import command, hoặc recompile jobs bắt buộc phải sử dụng hàm shell_exec hoặc exec để chạy các tiến trình nền (ví dụ: composer audit, node build).';

                    array_unshift($manualChecks, 'Xác nhận xem hàm exec/shell_exec đó được gọi từ dòng lệnh hợp lý hay không.', 'Kiểm tra lịch sử git commit gần nhất của tệp xem có bất kỳ thay đổi lạ nào từ tác giả khác không.');
                    array_unshift($remediationSteps, 'Giữ nguyên tệp và không thực hiện chỉnh sửa nếu hàm này phục vụ tính năng nghiệp vụ cần thiết.');
                    array_unshift($nextActions, 'Đánh dấu "Bỏ qua" hoặc "Đã duyệt" cho tệp tin cho phép (allowlist) này.');
                } else {
                    // Check for combined patterns like eval(base64_decode)
                    $hasEval = false;
                    $hasBase64 = false;
                    $snippet = $evidence['snippet'] ?? '';
                    if (stripos($snippet, 'eval') !== false) {
                        $hasEval = true;
                    }
                    if (stripos($snippet, 'base64_decode') !== false) {
                        $hasBase64 = true;
                    }

                    if ($hasEval && $hasBase64) {
                        $confidence = 'high';
                        $impact = 'Cực kỳ nguy hiểm (CRITICAL). Sự kết hợp giữa eval và base64_decode là đặc trưng tiêu biểu của webshell / mã độc backdoor ẩn giấu.';
                        $falsePositiveHint = 'Hầu như không có cảnh báo giả khi sử dụng eval(base64_decode(...)) trong mã nguồn dự án thực tế.';
                    } elseif ($hasBase64) {
                        $confidence = 'medium';
                        $impact = 'Cảnh báo trung bình (MEDIUM). base64_decode thường được các thư viện hợp pháp sử dụng để chuyển mã dữ liệu nhị phân hoặc chuỗi. Tuy nhiên, nó cũng có thể được dùng để giấu mã độc.';
                        $falsePositiveHint = 'Nếu tệp tin thuộc một thư viện mã nguồn uy tín hoặc Laravel helper, đây có thể là sử dụng thông thường.';
                    } else {
                        $confidence = 'high';
                    }

                    // Critical check: php in uploads or env in public
                    if (str_contains(strtolower($finding->path), 'uploads/') || str_contains(strtolower($finding->path), 'uploads\\')) {
                        $confidence = 'high';
                        $impact = 'Cực kỳ nguy hiểm (CRITICAL). Tệp PHP tồn tại trong thư mục tải lên công khai (uploads) thường là do tin tặc tải lên thông qua lỗ hổng upload để chạy webshell.';
                        $falsePositiveHint = 'Không bao giờ có cảnh báo giả đối với tệp PHP nằm trong thư mục uploads.';
                        array_unshift($remediationSteps, 'Sao lưu rồi tiến hành xóa ngay tệp PHP đó khỏi thư mục uploads.', 'Cấu hình lại webserver (.htaccess hoặc nginx config) để vô hiệu hóa việc thực thi tệp PHP trong thư mục uploads.');
                    }

                    if (str_contains(strtolower($finding->path), 'public/.env')) {
                        $confidence = 'high';
                        $impact = 'Cực kỳ nguy hiểm (CRITICAL). Tệp cấu hình .env nằm công khai trong thư mục public sẽ cho phép bất kỳ ai đọc được mật khẩu cơ sở dữ liệu, API keys, và khóa ứng dụng.';
                        $falsePositiveHint = 'Không có cảnh báo giả cho tệp .env công khai.';
                        array_unshift($remediationSteps, 'Di chuyển tệp .env ra thư mục gốc (nằm ngoài thư mục public). Thư mục public của Laravel chỉ nên chứa index.php và các tài sản tĩnh (css, js, images).');
                    }

                    array_unshift($manualChecks, 'Kiểm tra dòng code được phát hiện.', 'So sánh tệp hiện tại với tệp tin trên kho chứa Git (git diff) để tìm dòng code lạ.');
                    array_unshift($remediationSteps, 'Nếu phát hiện tệp tin bị tiêm mã lạ, hãy khôi phục lại từ Git hoặc xóa dòng mã độc hại sau khi sao lưu.');
                }
                break;

            case 'file_changes':
                $whyFlagged = 'Hệ thống so sánh mã băm (MD5 hash) của tệp tin hiện tại với mã băm đã lưu trong tệp Baseline. Mọi tệp tin bị sửa đổi nội dung, tệp tin bị xóa hoặc tệp tin mới xuất hiện đều sẽ bị cảnh báo để đảm bảo tính toàn vẹn của mã nguồn.';
                $impact = 'Nguy hiểm trung bình/cao (MEDIUM/HIGH). Sự thay đổi tệp tin có thể do hacker can thiệp ghi đè tệp hoặc do lập trình viên vừa chỉnh sửa mã nguồn trực tiếp trên máy chủ.';
                $falsePositiveHint = 'Đây là cảnh báo giả nếu bạn hoặc lập trình viên vừa triển khai (deploy) bản cập nhật mã nguồn mới mà chưa cập nhật lại Baseline.';

                $confidence = 'medium';
                array_unshift($manualChecks, 'Xác nhận xem tệp tin bị thay đổi có nằm trong đợt triển khai code/deploy gần đây không.', 'Mở tệp và đối chiếu thay đổi so với Git repository.');
                array_unshift($remediationSteps, 'Nếu việc thay đổi tệp là hợp lệ do bạn cập nhật tính năng, hãy nhấn nút "Tạo baseline" để hệ thống ghi nhận mã băm mới làm mốc đối chiếu.', 'Nếu thay đổi không rõ nguyên nhân, khôi phục lại tệp tin sạch từ Git hoặc bản backup gần nhất.');
                break;

            case 'public_files':
                $whyFlagged = 'Các tệp tin cấu hình (.env), tệp tin lịch sử/quản lý gói (composer.json, composer.lock, package.json), thư mục ẩn chứa mã nguồn (.git) hoặc các tệp tin backup (.sql, .zip) không được phép nằm công khai trong thư mục public vì người dùng bên ngoài có thể tải trực tiếp về.';
                $impact = 'Nguy hiểm cao đến cực kỳ nguy hiểm (HIGH/CRITICAL). Việc lộ tệp cấu hình .env hoặc tệp backup dữ liệu có thể dẫn tới rò rỉ toàn bộ cơ sở dữ liệu của phòng khám.';
                $falsePositiveHint = 'Không có cảnh báo giả cho các tệp nhạy cảm công khai. Cần chặn truy cập ngay lập tức.';

                $confidence = 'high';
                array_unshift($manualChecks, 'Truy cập trực tiếp đường dẫn của tệp trên trình duyệt để kiểm tra xem có tải về được không.', 'Kiểm tra cấu hình thư mục gốc của Web Server (Apache/Nginx) xem có trỏ trực tiếp vào thư mục public của Laravel hay trỏ nhầm vào thư mục gốc của dự án.');
                array_unshift($remediationSteps, 'Đảm bảo Web Server được cấu hình trỏ thư mục gốc (Document Root) vào thư mục "public/" của dự án.', 'Xóa các tệp backup (.sql, .zip) hoặc tệp tin kiểm tra thử (phpinfo.php, test.php) khỏi thư mục public.', 'Thêm cấu hình chặn trong tệp .htaccess hoặc nginx.conf để chặn truy cập đến các tệp nhạy cảm này.');
                break;

            case 'server_status':
                $whyFlagged = 'Hệ thống kiểm tra các cấu hình phân quyền ghi tệp và các thiết lập PHP bảo mật trên máy chủ (như chế độ gỡ lỗi APP_DEBUG, phiên bản PHP cũ).';
                $impact = 'Nguy hiểm trung bình (MEDIUM). Chế độ gỡ lỗi bật ở môi trường thực tế sẽ làm lộ mã nguồn và mật khẩu cấu hình khi xảy ra lỗi. Phân quyền ghi không đúng có thể khiến hệ thống không hoạt động được hoặc tạo cơ hội cho kẻ xấu ghi đè tệp tin.';

                $isLocal = config('app.env') === 'local';
                if ($isLocal) {
                    $confidence = 'low';
                    $falsePositiveHint = 'Nếu bạn đang chạy trên máy tính cá nhân (localhost) để phát triển phần mềm, việc bật APP_DEBUG=true là bình thường để dễ dàng sửa lỗi.';
                } else {
                    $confidence = 'high';
                    $falsePositiveHint = 'Không có cảnh báo giả khi chạy môi trường thực tế (production). Bắt buộc phải tắt chế độ debug.';
                }

                array_unshift($manualChecks, 'Kiểm tra cấu hình môi trường APP_ENV và APP_DEBUG trong tệp .env của máy chủ.', 'Kiểm tra phân quyền (permissions) của thư mục storage và bootstrap/cache.');
                array_unshift($remediationSteps, 'Sửa cấu hình trong tệp .env trên máy chủ thực tế: APP_DEBUG=false.', 'Sau khi sửa .env, chạy lệnh: php artisan optimize:clear để áp dụng cấu hình mới.', 'Cấp quyền ghi cho thư mục storage và bootstrap/cache bằng lệnh chmod thích hợp trên hosting (ví dụ: chmod -R 775 storage).');
                break;

            case 'content_safety':
                $whyFlagged = 'Nội dung bài viết hoặc các cấu hình giao diện chứa thẻ tin script đáng ngờ (<script>, iframe cá cược, mã script độc hại). Đây là dấu hiệu của việc cơ sở dữ liệu bị tiêm nhiễm mã độc XSS nhằm chèn quảng cáo bẩn hoặc đánh cắp cookie của người dùng.';
                $impact = 'Nguy hiểm cao đến cực kỳ nguy hiểm (HIGH/CRITICAL). XSS có thể chuyển hướng khách hàng truy cập website của phòng khám sang các trang web lừa đảo hoặc đánh cắp tài khoản quản trị khi admin xem bài viết.';
                $falsePositiveHint = 'Có thể là cảnh báo giả nếu bạn cố tình chèn iframe của Google Maps (bản đồ phòng khám) hoặc YouTube (video giới thiệu) hợp lệ vào bài viết.';

                $confidence = 'medium';
                if (isset($meta['field']) && str_contains(strtolower($meta['field']), 'settings')) {
                    $confidence = 'high';
                }

                array_unshift($manualChecks, 'Sử dụng liên kết sửa bài viết/cài đặt được báo cáo trong bằng chứng để xem nội dung thực tế.', 'Xem bài viết trên giao diện công khai để xem có xuất hiện hành vi chuyển hướng hoặc quảng cáo lạ không.');
                array_unshift($remediationSteps, 'Mở giao diện sửa bài viết tương ứng trên trang quản trị, chuyển sang chế độ xem mã nguồn HTML (Source Code) và xóa bỏ các thẻ script lạ.', 'Nếu là cài đặt hệ thống bị nhiễm mã độc, truy cập Cài đặt và dọn dẹp giá trị cấu hình tương ứng.');
                break;

            case 'password_strength':
                $whyFlagged = 'Mật khẩu yếu, tài khoản admin sử dụng mật khẩu mặc định (password), hoặc mã băm mật khẩu rỗng/lỗi là những kẽ hở lớn nhất để kẻ tấn công dò tìm mật khẩu (brute-force) và xâm nhập trang quản trị.';
                $impact = 'Nguy hiểm cực kỳ nghiêm trọng (CRITICAL). Chiếm được tài khoản admin đồng nghĩa với việc kiểm soát hoàn toàn hệ thống.';
                $falsePositiveHint = 'Không có cảnh báo giả cho mật khẩu yếu hay tài khoản admin dùng mật khẩu mặc định.';

                $confidence = 'high';
                array_unshift($manualChecks, 'Kiểm tra xem tài khoản admin@dakhoacantho.com có đổi mật khẩu mặc định chưa.', 'Kiểm tra nhật ký đăng nhập thất bại gần đây của hệ thống.');
                array_unshift($remediationSteps, 'Đổi ngay mật khẩu của tài khoản admin@dakhoacantho.com thành mật khẩu mạnh (chứa chữ hoa, chữ thường, số và ký tự đặc biệt).', 'Kích hoạt chính sách mật khẩu mạnh trong Laravel và Filament.', 'Kích hoạt xác thực 2 lớp (2FA) cho toàn bộ tài khoản quản trị nếu có thể.');
                break;

            case 'vulnerability_scan':
                $whyFlagged = 'Cảnh báo này kiểm tra các lỗ hổng bảo mật đã biết của các thư viện PHP bên thứ ba (được khai báo trong composer.json) thông qua công cụ composer audit.';
                $impact = 'Nguy hiểm trung bình/cao (MEDIUM/HIGH). Sử dụng thư viện lỗi thời chứa lỗ hổng bảo mật có thể bị khai thác trực tiếp từ bên ngoài.';

                $isNotChecked = str_contains(strtolower($finding->message), 'không thể hoàn thành') || str_contains(strtolower($finding->message), 'lỗi phản hồi');
                if ($isNotChecked) {
                    $confidence = 'low';
                    $falsePositiveHint = 'Đây là trạng thái "Chưa kiểm tra được" (Not Checked) do máy chủ không cài sẵn Composer hoặc bị chặn hàm hệ thống. Đây không phải là cảnh báo lỗ hổng thực tế.';
                    array_unshift($manualChecks, 'Chạy thử lệnh "composer audit" thủ công trên máy tính cá nhân ở thư mục dự án tương đương.');
                    array_unshift($remediationSteps, 'Chạy lệnh "rtk composer audit" thủ công trên máy chủ nếu có quyền dòng lệnh để tự kiểm tra.');
                } else {
                    $confidence = 'high';
                    $falsePositiveHint = 'Cảnh báo lỗ hổng là chính xác dựa trên cơ sở dữ liệu lỗ hổng bảo mật của Packagist.';
                    array_unshift($manualChecks, 'Đọc thông tin lỗ hổng được báo cáo trong mục bằng chứng.', 'Kiểm tra tài liệu xem phiên bản nào đã vá lỗi.');
                    array_unshift($remediationSteps, 'Thực hiện sao lưu mã nguồn và cơ sở dữ liệu trước.', 'Chạy lệnh nâng cấp thư viện lỗi lên phiên bản an toàn hơn: composer update <ten_thu_vien>.', 'Kiểm tra lại tính ổn định của ứng dụng sau khi cập nhật.');
                }
                break;

            case 'user_check':
                $whyFlagged = 'Kiểm tra số lượng tài khoản có quyền admin, phát hiện email trùng lặp, hoặc tài khoản admin không hoạt động lâu ngày nhằm hạn chế rủi ro lộ tài khoản.';
                $impact = 'Nguy hiểm trung bình (MEDIUM). Số lượng admin quá nhiều hoặc email trùng lặp làm tăng bề mặt tấn công và rủi ro rò rỉ tài khoản.';
                $falsePositiveHint = 'Đây có thể là hành vi bình thường nếu phòng khám có nhiều nhân viên quản trị cần quyền truy cập độc lập.';

                $confidence = 'medium';
                array_unshift($manualChecks, 'Kiểm tra danh sách người dùng quản trị xem có tài khoản nào lạ hoặc không còn sử dụng không.', 'Kiểm tra trùng lặp email.');
                array_unshift($remediationSteps, 'Thu hồi quyền admin đối với các tài khoản không thực sự cần thiết, chuyển quyền của họ về mức Editor hoặc nhân viên.', 'Xóa các tài khoản trùng lặp hoặc không hoạt động lâu ngày.');
                break;

            case 'blocklist_check':
                $whyFlagged = 'Địa chỉ IP của máy chủ được kiểm tra đối chiếu với các tổ chức chống spam (DNSBL như Spamhaus, Sorbs). Nếu IP máy chủ nằm trong danh sách đen, email gửi từ website của phòng khám có thể bị chặn hoặc rơi vào thư mục thư rác (spam).';
                $impact = 'Cảnh báo cảnh giác mức thấp (LOW). Không ảnh hưởng đến bảo mật trực tiếp của mã nguồn hay cơ sở dữ liệu, nhưng ảnh hưởng đến khả năng gửi email và uy tín tên miền.';
                $falsePositiveHint = 'Nếu website của bạn dùng chung IP (Shared Hosting), một website khác trên cùng hosting phát tán spam cũng có thể khiến IP này bị liệt vào danh sách đen. Đây là trường hợp cảnh báo giả phổ biến trên shared hosting.';

                $confidence = 'low';
                array_unshift($manualChecks, 'Kiểm tra xem tên miền phòng khám có gửi email xác nhận lịch hẹn bình thường không.', 'Kiểm tra IP máy chủ trên các trang công cụ kiểm tra blacklist (như mxtoolbox.com).');
                array_unshift($remediationSteps, 'Không cần thay đổi mã nguồn.', 'Sử dụng các dịch vụ gửi email bên thứ ba (SMTP tin cậy như SendGrid, Mailgun, Amazon SES) thay vì gửi email trực tiếp từ máy chủ hosting để đảm bảo email không bị rơi vào hòm thư rác.', 'Yêu cầu nhà cung cấp hosting đổi IP sạch khác nếu tình trạng rơi vào danh sách đen kéo dài.');
                break;

            case 'security_options':
                $whyFlagged = 'Kiểm tra các cấu hình cài đặt HTTPS, thuộc tính bảo mật session cookie nhằm bảo vệ các dữ liệu giao tiếp và thông tin đăng nhập của phiên làm việc.';
                $impact = 'Nguy hiểm trung bình/cao (MEDIUM/HIGH). Nếu chạy website trên giao thức HTTP thường hoặc không bật Secure cookie, thông tin tài khoản admin có thể bị nghe lén (sniffing) và đánh cắp dễ dàng trên các mạng Wi-Fi công cộng.';
                $falsePositiveHint = 'Có thể coi là cảnh báo giả tạm thời nếu bạn đang chạy thử nghiệm ứng dụng ở môi trường local phát triển dưới giao thức HTTP.';

                $confidence = 'high';
                array_unshift($manualChecks, 'Truy cập trang web và xem biểu tượng ổ khóa bảo mật trên thanh địa chỉ.', 'Kiểm tra thiết lập SESSION_SECURE_COOKIE trong cấu hình .env.');
                array_unshift($remediationSteps, 'Cài đặt chứng chỉ SSL (Let\'s Encrypt miễn phí hoặc trả phí) cho tên miền.', 'Cấu hình chuyển hướng tự động toàn bộ người dùng từ HTTP sang HTTPS.', 'Đảm bảo tệp .env có cấu hình: SESSION_SECURE_COOKIE=true.');
                break;
        }

        // Map confidence keys to Vietnamese labels and background colors
        $confidenceLabels = [
            'low' => 'Thấp (Khả năng cao là cảnh báo giả)',
            'medium' => 'Trung bình (Cần xác minh)',
            'high' => 'Cao (Khả năng thực tế cao)',
        ];

        return [
            'summary' => $summary,
            'why_flagged' => $whyFlagged,
            'impact' => $impact,
            'confidence' => $confidence,
            'confidence_label' => $confidenceLabels[$confidence] ?? 'Trung bình',
            'evidence' => $evidence,
            'false_positive_hint' => $falsePositiveHint,
            'manual_checks' => $manualChecks,
            'remediation_steps' => $remediationSteps,
            'next_actions' => $nextActions,
        ];
    }

    /**
     * Check if a path belongs to allowlisted system scripts that are expected to use shell commands.
     */
    protected function isAllowlistedSystemFile(string $path): bool
    {
        $normalized = str_replace('\\', '/', $path);

        $allowlist = [
            'app/Services/Security/SecurityScannerService.php',
            'app/Console/Commands/ImportOldArticles.php',
            'app/Console/Commands/RemapArticleCategories.php',
            'app/Services/WordPress/WordPressImportService.php',
            'app/Jobs/RecompileUrlPathsJob.php',
        ];

        foreach ($allowlist as $file) {
            if (str_contains($normalized, $file)) {
                return true;
            }
        }

        return false;
    }
}
