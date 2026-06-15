<script>
    (function () {
        // Listen for Livewire dispatch event 'open-preview' from the previewArticle() method
        // This opens the preview URL in a new tab after session data is saved
        if (typeof Livewire !== 'undefined') {
            Livewire.on('open-preview', (payload) => {
                const previewUrl = typeof payload === 'string' ? payload : payload?.url;

                if (!previewUrl) {
                    console.error('[preview] Preview URL missing:', payload);
                    alert('Không thể mở bản xem trước vì thiếu đường dẫn.');
                    return;
                }

                console.log('[preview] Opening preview URL:', previewUrl);

                if (window.articlePreviewWindow && !window.articlePreviewWindow.closed) {
                    window.articlePreviewWindow.location.href = previewUrl;
                    window.articlePreviewWindow.focus();
                    return;
                }

                window.open(previewUrl, '_blank');
            });
        }

        // Define the global trigger function to initiate article preview via Livewire
        window.triggerArticlePreview = function (wire, el) {
            if (!wire) {
                console.error('[preview] Livewire component not available');
                alert('Không thể khởi tạo bản xem trước.');
                return;
            }

            window.articlePreviewWindow = window.open('about:blank', '_blank');

            if (!window.articlePreviewWindow) {
                alert('Trình duyệt đang chặn popup. Vui lòng cho phép popup để mở bản xem trước.');
                return;
            }

            // Dispatch 'sync-tinymce-editors' event with triggerPreview flag
            // TinyMCE editor listener will:
            // 1. Upload any pending blob images
            // 2. Sync editor content to Livewire state
            // 3. Call wire.previewArticle(content) to trigger the preview
            window.dispatchEvent(new CustomEvent('sync-tinymce-editors', {
                detail: {
                    triggerPreview: true
                }
            }));
        };
    })();
</script>
