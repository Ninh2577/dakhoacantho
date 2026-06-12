<script>
(function () {
    // Define the global trigger function to avoid HTML escaping issues in Blade templates
    window.triggerArticlePreview = function (wire) {
        window.articlePreviewWindow = window.open('about:blank', '_blank');

        let editorHandled = false;
        if (typeof tinymce !== 'undefined') {
            let editors = tinymce.get();
            if (editors && editors.length > 0) {
                let activeEditor = editors.find(e => !e.isHidden());
                if (activeEditor) {
                    editorHandled = true;
                }
            }
        }

        if (editorHandled) {
            window.dispatchEvent(new CustomEvent('sync-tinymce-editors', { detail: { triggerPreview: true } }));
        } else {
            console.warn('[preview] TinyMCE editor not found or not ready, falling back to direct preview');
            wire.previewArticle();
        }
    };

    if (window.__openPreviewListenerRegistered) {
        return;
    }

    window.__openPreviewListenerRegistered = true;

    window.addEventListener('open-preview', function (event) {
        let url = null;

        if (event.detail && event.detail.url) {
            url = event.detail.url;
        } else if (Array.isArray(event.detail) && event.detail[0] && event.detail[0].url) {
            url = event.detail[0].url;
        } else if (typeof event.detail === 'string') {
            url = event.detail;
        } else if (event.detail && typeof event.detail === 'object') {
            url = event.detail.url || Object.values(event.detail)[0];
        }

        console.debug('[preview] open-preview received', event.detail, url);

        if (!url) {
            console.error('[preview] missing URL', event.detail);
            if (window.articlePreviewWindow) {
                try { window.articlePreviewWindow.close(); } catch (e) {}
                window.articlePreviewWindow = null;
            }
            return;
        }

        if (window.articlePreviewWindow && !window.articlePreviewWindow.closed) {
            window.articlePreviewWindow.location.href = url;
            try { window.articlePreviewWindow.focus(); } catch (e) {}
            window.articlePreviewWindow = null;
        } else {
            window.open(url, '_blank', 'noopener,noreferrer');
        }
    });
})();
</script>
