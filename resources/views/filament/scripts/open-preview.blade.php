<script>
    (function () {
        // Listen for Livewire dispatch event 'open-preview' from the previewArticle() method
        // This opens the preview URL in a new tab after session data is saved
        const handleOpenPreview = (url) => {
            if (!url) return;
            console.log('[preview] Redirecting to preview URL:', url);

            if (window.articlePreviewWindow && !window.articlePreviewWindow.closed) {
                window.articlePreviewWindow.location.href = url;
                window.articlePreviewWindow.focus();
            } else {
                window.open(url, '_blank');
            }
        };

        if (typeof Livewire !== 'undefined') {
            Livewire.on('open-preview', (payload) => {
                let previewUrl = null;
                if (typeof payload === 'string') {
                    previewUrl = payload;
                } else if (payload && typeof payload === 'object') {
                    if (payload.url) {
                        previewUrl = payload.url;
                    } else if (Array.isArray(payload)) {
                        const first = payload[0];
                        previewUrl = typeof first === 'string' ? first : first?.url;
                    } else if (payload.detail && payload.detail.url) {
                        previewUrl = payload.detail.url;
                    } else {
                        previewUrl = Object.values(payload)[0];
                    }
                }

                if (!previewUrl) {
                    console.error('[preview] Preview URL missing in payload:', payload);
                    if (window.articlePreviewWindow) {
                        try { window.articlePreviewWindow.close(); } catch (e) {}
                        window.articlePreviewWindow = null;
                    }
                    alert('Không thể mở bản xem trước vì thiếu đường dẫn.');
                    return;
                }

                handleOpenPreview(previewUrl);
            });

            Livewire.on('open-preview-failed', () => {
                console.log('[preview] Preview failed, closing preview window.');
                if (window.articlePreviewWindow) {
                    try { window.articlePreviewWindow.close(); } catch (e) {}
                    window.articlePreviewWindow = null;
                }
            });
        }

        // Also add native window listener just in case
        window.addEventListener('open-preview', (event) => {
            const previewUrl = event.detail?.url || event.detail?.[0] || event.detail;
            if (previewUrl) {
                handleOpenPreview(previewUrl);
            }
        });

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
