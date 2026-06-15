<script>
(function () {
    // Define the global trigger function to submit form data synchronously to the preview URL via POST
    window.triggerArticlePreview = function (wire, el) {
        let form = null;
        if (el) {
            form = el.closest('form');
        }
        if (!form) {
            form = document.querySelector('form');
        }
        if (!form) {
            console.error('[preview] Active form not found');
            alert('Không tìm thấy biểu mẫu để xem trước.');
            return;
        }

        // Synchronize TinyMCE editor state back to underlying textareas
        if (typeof tinymce !== 'undefined') {
            try {
                tinymce.triggerSave();
            } catch (e) {
                console.error('[preview] TinyMCE triggerSave error:', e);
            }
        }

        // Save original form attributes to restore them immediately after submission
        let originalAction = form.getAttribute('action');
        let originalTarget = form.getAttribute('target');
        let originalMethod = form.getAttribute('method');

        // Ensure CSRF token is present in the form to prevent "419 Page Expired"
        let csrfInput = form.querySelector('input[name="_token"]');
        let tempCsrf = null;
        if (!csrfInput) {
            csrfInput = document.createElement('input');
            csrfInput.setAttribute('type', 'hidden');
            csrfInput.setAttribute('name', '_token');
            csrfInput.setAttribute('value', '{{ csrf_token() }}');
            form.appendChild(csrfInput);
            tempCsrf = csrfInput;
        }

        // Set action, target, and method for POST preview submit in a new tab
        form.setAttribute('action', '{{ url("/admin/articles/preview-create") }}');
        form.setAttribute('target', '_blank');
        form.setAttribute('method', 'POST');

        // Submit natively to bypass Livewire interceptors
        try {
            form.submit();
        } catch (e) {
            console.error('[preview] Form submit error:', e);
            alert('Không thể mở bản xem trước: ' + e.message);
        }

        // Clean up temporary CSRF token
        if (tempCsrf) {
            form.removeChild(tempCsrf);
        }

        // Restore original form attributes
        if (originalAction !== null) {
            form.setAttribute('action', originalAction);
        } else {
            form.removeAttribute('action');
        }

        if (originalTarget !== null) {
            form.setAttribute('target', originalTarget);
        } else {
            form.removeAttribute('target');
        }

        if (originalMethod !== null) {
            form.setAttribute('method', originalMethod);
        } else {
            form.removeAttribute('method');
        }
    };
})();
</script>
