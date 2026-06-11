<div
    x-data="{
        state: $wire.entangle('{{ $getStatePath() }}'),
        editorInstanceId: null,
        activeTab: 'visual',
        editorReady: false,
        
        init() {
            this.$nextTick(() => {
                if (typeof tinymce === 'undefined') {
                    let script = document.createElement('script');
                    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js';
                    script.referrerpolicy = 'origin';
                    script.onload = () => this.initEditor();
                    document.head.appendChild(script);
                } else {
                    this.initEditor();
                }
            });

            // Watch for external Livewire state changes to update the editor content
            this.$watch('state', (newVal) => {
                if (this.editorInstanceId) {
                    let editor = tinymce.get(this.editorInstanceId);
                    if (editor && this.editorReady) {
                        if (this.activeTab === 'visual' && !editor.hasFocus() && newVal !== editor.getContent()) {
                            editor.setContent(newVal || '');
                            // Also ensure editor stays in design mode after state update
                            try { editor.mode.set('design'); } catch (e) {}
                        }
                    }
                }
            });
        },
        
        initEditor() {
            let id = this.$refs.editor.id;
            let oldEditor = id ? tinymce.get(id) : null;
            if (oldEditor) {
                oldEditor.remove();
            }

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
                images_upload_url: '{{ route('admin.tinymce.upload-image', [], false) }}',
                images_upload_credentials: true,
                automatic_uploads: true,
                images_upload_handler: (blobInfo, progress) => {
                    return new Promise((resolve, reject) => {
                        let xhr = new XMLHttpRequest();
                        xhr.withCredentials = true;
                        xhr.open('POST', '{{ route('admin.tinymce.upload-image', [], false) }}');
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

                    // Helper: apply content and force editable design mode
                    const ensureEditorReady = () => {
                        try {
                            editor.mode.set('design');
                            let body = editor.getBody();
                            if (body) {
                                body.setAttribute('contenteditable', 'true');
                            }
                        } catch (e) {}
                    };

                    // Load initial value with delayed retries
                    // The iframe body needs time to fully render before it becomes editable
                    editor.on('init', () => {
                        // Phase 1: Immediate attempt
                        editor.setContent(this.state || '');
                        ensureEditorReady();

                        // Phase 2: Retry after iframe is fully rendered (fixes blank Visual tab)
                        setTimeout(() => {
                            if (this.state && editor.getContent() !== this.state) {
                                editor.setContent(this.state || '');
                            }
                            ensureEditorReady();
                            this.editorReady = true;
                        }, 150);

                        // Phase 3: Final retry for slow Livewire hydration on edit pages
                        setTimeout(() => {
                            if (this.state && editor.getContent() !== this.state) {
                                editor.setContent(this.state || '');
                            }
                            ensureEditorReady();
                        }, 600);
                    });

                    // Debounced state sync on typing
                    let debouncedUpdate = this.debounce(() => {
                        this.state = editor.getContent();
                    }, 300);

                    editor.on('change keyup undo redo input', () => {
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
                                if (this.activeTab === 'visual') {
                                    this.state = editor.getContent();
                                } else {
                                    this.state = this.$refs.editor.value;
                                }
                            });
                        }
                    });
                }
            });
        },
        
        switchTab(tab) {
            if (this.activeTab === tab) return;
            
            let editor = this.editorInstanceId ? tinymce.get(this.editorInstanceId) : null;
            
            if (tab === 'text') {
                if (editor) {
                    editor.save(); // Sync content to textarea
                    this.state = editor.getContent();
                    editor.hide(); // Hide visual editor and show styled textarea
                }
                this.activeTab = 'text';
            } else {
                if (editor) {
                    editor.show(); // Hide textarea and show visual editor
                    editor.setContent(this.state || '');
                    // Force design mode and contenteditable
                    try {
                        editor.mode.set('design');
                        editor.getBody().setAttribute('contenteditable', 'true');
                    } catch (e) {}
                    setTimeout(() => {
                        editor.focus();
                    }, 100);
                }
                this.activeTab = 'visual';
            }
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
    class="w-full border border-slate-300 rounded-lg overflow-hidden flex flex-col"
    style="border-color: #cbd5e1;"
>
    <!-- Tab Switcher (WordPress style) -->
    <div class="flex justify-end bg-slate-50 border-b border-slate-200 px-4 pt-2 gap-1 select-none">
        <button 
            type="button" 
            @click="switchTab('visual')"
            :class="activeTab === 'visual' ? 'bg-white border-t border-x border-slate-300 text-slate-800 font-extrabold -mb-px' : 'text-slate-500 hover:text-slate-800'"
            class="px-4 py-1.5 text-xs rounded-t-md transition border-transparent border-t border-x"
        >
            Trực quan (Visual)
        </button>
        <button 
            type="button" 
            @click="switchTab('text')"
            :class="activeTab === 'text' ? 'bg-white border-t border-x border-slate-300 text-slate-800 font-extrabold -mb-px' : 'text-slate-500 hover:text-slate-800'"
            class="px-4 py-1.5 text-xs rounded-t-md transition border-transparent border-t border-x"
        >
            Văn bản (Text)
        </button>
    </div>

    <!-- Editor Wrapper -->
    <div class="relative w-full flex-grow">
        <!-- Single Textarea that gets enhanced by TinyMCE -->
        <textarea 
            id="tinymce-content-{{ $getId() }}"
            x-ref="editor" 
            x-model.lazy="state"
            @input="state = $el.value"
            class="w-full font-mono p-4 text-sm bg-slate-950 text-slate-200 focus:outline-none focus:ring-0 border-0"
            style="height: 500px; min-height: 450px; font-family: Consolas, Monaco, monospace; line-height: 1.6; resize: vertical; display: block;"
            placeholder="Nhập nội dung bài viết..."
        ></textarea>
    </div>
</div>
