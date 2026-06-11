<div
    x-data="{
        state: $wire.entangle('{{ $getStatePath() }}'),
        editorInstanceId: null,
        init() {
            // Load TinyMCE from public community CDN if not already loaded
            if (typeof tinymce === 'undefined') {
                let script = document.createElement('script');
                script.src = 'https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js';
                script.referrerpolicy = 'origin';
                script.onload = () => this.initEditor();
                document.head.appendChild(script);
            } else {
                this.initEditor();
            }

            // Watch for external Livewire state changes to update the editor content
            this.$watch('state', (newVal) => {
                if (this.editorInstanceId) {
                    let editor = tinymce.get(this.editorInstanceId);
                    if (editor && editor.initialized && !editor.hasFocus() && newVal !== editor.getContent()) {
                        editor.setContent(newVal || '');
                    }
                }
            });
        },
        initEditor() {
            tinymce.init({
                target: this.$refs.editor,
                height: 500,
                min_height: 450,
                branding: false,
                promotion: false,
                convert_urls: false,
                relative_urls: false,
                remove_script_host: false,
                menubar: 'file edit view insert format tools table',
                plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount directionality emoticons codesample',
                toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | blockquote | link image media table | removeformat searchreplace code preview fullscreen',
                toolbar_sticky: true,
                toolbar_sticky_offset: 60,
                content_style: 'body { font-family: Arial, sans-serif; font-size: 16px; line-height: 1.7; color: #334155; }',
                
                // Admin image upload integration
                images_upload_url: '{{ route('admin.tinymce.upload-image') }}',
                images_upload_credentials: true,
                automatic_uploads: true,
                images_upload_handler: (blobInfo, progress) => {
                    return new Promise((resolve, reject) => {
                        let xhr = new XMLHttpRequest();
                        xhr.withCredentials = false;
                        xhr.open('POST', '{{ route('admin.tinymce.upload-image') }}');
                        xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                        xhr.upload.onprogress = (e) => {
                            progress(e.loaded / e.total * 100);
                        };

                        xhr.onload = () => {
                            if (xhr.status === 403) {
                                reject({ message: 'Lỗi 403: Không có quyền truy cập', remove: true });
                                return;
                            }
                            if (xhr.status < 200 || xhr.status >= 300) {
                                reject('Lỗi HTTP: ' + xhr.status);
                                return;
                            }
                            let json = JSON.parse(xhr.responseText);
                            if (!json || typeof json.location !== 'string') {
                                reject('Lỗi phản hồi JSON từ server.');
                                return;
                            }
                            resolve(json.location);
                        };

                        xhr.onerror = () => {
                            reject('Lỗi kết nối mạng khi tải ảnh lên.');
                        };

                        let formData = new FormData();
                        formData.append('image', blobInfo.blob(), blobInfo.filename());
                        xhr.send(formData);
                    });
                },
                setup: (editor) => {
                    // Save instance ID to refer in destroy() and watches
                    this.editorInstanceId = editor.id;

                    // Load initial value
                    editor.on('init', () => {
                        editor.setContent(this.state || '');
                    });

                    // Debounced state sync on typing
                    let debouncedUpdate = this.debounce(() => {
                        this.state = editor.getContent();
                    }, 300);

                    editor.on('change keyup undo redo', () => {
                        debouncedUpdate();
                    });

                    editor.on('blur', () => {
                        this.state = editor.getContent();
                    });

                    // Form submit sync fallback
                    editor.on('submit', () => {
                        this.state = editor.getContent();
                    });

                    // Bind to parent form submit to immediately sync state before Livewire submits
                    this.$nextTick(() => {
                        let form = this.$el.closest('form');
                        if (form) {
                            form.addEventListener('submit', () => {
                                this.state = editor.getContent();
                            });
                        }
                    });
                }
            });
        },
        destroy() {
            // Clean up TinyMCE instance when leaving the page (essential for Filament SPA navigation)
            if (this.editorInstanceId) {
                let editor = tinymce.get(this.editorInstanceId);
                if (editor) {
                    editor.remove();
                }
            }
        },
        debounce(func, wait) {
            let timeout;
            return function () {
                let context = this, args = arguments;
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    func.apply(context, args);
                }, wait);
            };
        }
    }"
    x-init="init()"
    x-on:destroy="destroy()"
    wire:ignore
    class="w-full border border-slate-300 rounded-lg overflow-hidden"
    style="border-color: #dbeafe;"
>
    <textarea x-ref="editor" class="w-full" style="visibility: hidden; height: 500px; display: block;"></textarea>
</div>
