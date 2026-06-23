@php
    $safeId = str_replace('.', '-', $getId());
@endphp
<div
    id="tinymce-wrapper-{{ $safeId }}"
    wire:key="tinymce-wrapper-{{ $safeId }}"
    x-data="tinymceEditor({
        state: $wire.entangle('{{ $getStatePath() }}'),
        statePath: '{{ $getStatePath() }}',
        uploadUrl: '{{ route('admin.tinymce.upload-image') }}',
        searchUrl: '{{ route('admin.internal-links.search') }}',
        csrfToken: '{{ csrf_token() }}',
        excludeId: '{{ optional($this->record)->id ?? '' }}'
    })"
    x-init="init()"
    x-on:destroy="destroy()"
    wire:ignore
    class="tinymce-editor-container w-full border border-slate-300 rounded-lg flex flex-col"
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
            id="tinymce-content-{{ $safeId }}"
            x-ref="editor" 
            x-model.lazy="state"
            @input="state = $el.value"
            class="w-full font-mono p-4 text-sm bg-slate-950 text-slate-200 focus:outline-none focus:ring-0 border-0"
            style="height: 900px; min-height: 800px; font-family: Consolas, Monaco, monospace; line-height: 1.6; resize: vertical; display: block;"
            placeholder="Nhập nội dung bài viết..."
        ></textarea>
    </div>

<script>
(function() {
    window.tinyMCEPreInit = {
        baseURL: 'https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2',
        suffix: '.min'
    };

    function registerTinyMceEditor() {
        if (typeof Alpine === 'undefined') return;
        Alpine.data('tinymceEditor', (config) => ({
            state: config.state,
            statePath: config.statePath,
            uploadUrl: config.uploadUrl,
            searchUrl: config.searchUrl,
            csrfToken: config.csrfToken,
            excludeId: config.excludeId || '',
            editorInstanceId: null,
            activeTab: 'visual',
            editorReady: false,
            _pendingContent: null,
            _syncAbortController: null,
            _visibilityObserver: null,
            _livewireCommitHook: null,
            _formElement: null,
            _formSubmitHandler: null,
            
            init() {
                console.log('TINYMCE INIT:', config.statePath);
                // Delay initialization to avoid race conditions with Alpine/Livewire DOM hydration on page load
                setTimeout(() => {
                    window.tinyMCEPreInit = {
                        baseURL: 'https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2',
                        suffix: '.min'
                    };

                    if (typeof tinymce === 'undefined') {
                        let script = document.createElement('script');
                        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/tinymce.min.js';
                        script.referrerpolicy = 'origin';
                        script.onload = () => this.tryInitEditor();
                        document.head.appendChild(script);
                    } else {
                        this.tryInitEditor();
                    }
                }, 300);

                // Watch for external Livewire state changes to update the editor content
                this.$watch('state', (newVal) => {
                    if (!this.editorReady) {
                        // Editor not ready yet — store content so we can apply it after init
                        if (newVal) {
                            this._pendingContent = newVal;
                        }
                        return;
                    }
                    if (this.editorInstanceId) {
                        let editor = tinymce.get(this.editorInstanceId);
                        if (editor && this.activeTab === 'visual' && !editor.hasFocus() && newVal !== editor.getContent()) {
                            editor.setContent(newVal || '');
                            try {
                                if (editor.mode && typeof editor.mode.get === 'function' && editor.mode.get() !== 'design') {
                                    editor.mode.set('design');
                                }
                            } catch (e) {}
                        }
                    }
                });

                // Listen for sync-tinymce-editors event (used by preview trigger)
                // Use AbortController so the listener is removed on destroy() — prevents double-dispatch
                // that occurred when Livewire re-rendered and re-ran init() without removing old listeners.
                this._syncAbortController = new AbortController();
                window.addEventListener('sync-tinymce-editors', (event) => {
                    if (this.editorInstanceId) {
                        let editor = tinymce.get(this.editorInstanceId);
                        if (editor && this.editorReady) {
                            // Upload any pending blob images first, then sync + preview in ONE Livewire call
                            editor.uploadImages().then(() => {
                                let content = (this.activeTab === 'visual') ? editor.getContent() : this.$refs.editor.value;

                                // Keep Alpine state in sync (local only, no extra Livewire round-trip)
                                this.state = content;

                                // Check for lingering blobs to warn if uploads did not complete
                                if (content.includes('src="blob:')) {
                                    console.warn('TinyMCE image upload did not complete before preview');
                                }

                                if (event.detail && event.detail.triggerPreview) {
                                    // Pass content directly to previewArticle() in a SINGLE Livewire call.
                                    // This eliminates the race condition where a separate $wire.set() request
                                    // has not committed to $this->data before previewArticle() reads it.
                                    this.$wire.previewArticle(content);
                                } else {
                                    // Not a preview trigger — just sync state normally
                                    this.$wire.set(this.statePath, content);
                                }
                            }).catch((err) => {
                                console.error('TinyMCE pending upload error:', err);
                                if (window.articlePreviewWindow) {
                                    try { window.articlePreviewWindow.close(); } catch (e) {}
                                    window.articlePreviewWindow = null;
                                }
                                alert('Ảnh đang tải lên hoặc gặp lỗi khi tải lên, vui lòng thử lại sau.');
                            });
                        }
                    }
                }, { signal: this._syncAbortController.signal });

                // Giữ commit hook như backup (phòng trường hợp event không fire)
                if (typeof Livewire !== 'undefined' && typeof Livewire.hook === 'function') {
                    try {
                        this._livewireCommitHook = Livewire.hook('commit', () => {
                            if (!this.editorReady || !this.editorInstanceId) return;
                            try {
                                let editor = tinymce.get(this.editorInstanceId);
                                if (!editor) return;
                                const c = (this.activeTab === 'visual') ? editor.getContent() : this.state;
                                if (c !== null && c !== undefined) this.state = c;
                            } catch (e) {}
                        });
                    } catch (e) {
                        this._livewireCommitHook = null;
                    }
                }
            },

            tryInitEditor() {
                let el = this.$refs.editor;
                console.log('TEXTAREA FOUND', !!el);
                if (!el) return;

                // GUARD: wrapper phải đã connected vào DOM trước khi init
                // Nếu xậ y hơn, TinyMCE sẽ fallback vào document.body gây sidebar injection
                if (!this.$el.isConnected) {
                    console.warn('TINYMCE: wrapper chưa connected vào DOM, thử lại sau 100ms...');
                    setTimeout(() => this.tryInitEditor(), 100);
                    return;
                }

                if (el.offsetWidth > 0 && el.offsetHeight > 0) {
                    console.log('TINYMCE TEXTAREA VISIBLE, INITIALIZING...');
                    this.initEditor();
                } else {
                    console.log('TINYMCE TEXTAREA HIDDEN, WAITING FOR VISIBILITY...');
                    // Sử dụng IntersectionObserver để detect khi textarea hiển thị (ví dụ: khi tab active)
                    // Tuy nhiên cũng thêm một fallback timeout để tránh bị kẹt nếu observer không fire
                    let initCalled = false;
                    const doInit = () => {
                        if (initCalled) return;
                        initCalled = true;
                        if (this._visibilityObserver) {
                            this._visibilityObserver.disconnect();
                            this._visibilityObserver = null;
                        }
                        console.log('TINYMCE TEXTAREA BECOME VISIBLE, INITIALIZING NOW...');
                        this.initEditor();
                    };

                    const observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                doInit();
                                observer.disconnect();
                            }
                        });
                    }, { threshold: 0.01 });
                    this._visibilityObserver = observer;
                    observer.observe(el);

                    // Fallback: nếu sau 3 giây vẫn chưa init (tộc độ mạng chậm hoặc tab layout đặc biệt)
                    setTimeout(() => {
                        if (!initCalled) {
                            console.warn('TINYMCE: IntersectionObserver fallback triggered after 3s');
                            doInit();
                        }
                    }, 3000);
                }
            },
            
            initEditor() {
                let id = this.$refs.editor.id;
                let element = this.$refs.editor;
                console.log('TEXTAREA FOUND', !!element);
                if (!element) return;

                console.log('TINYMCE INIT START', id);

                let oldEditor = id ? tinymce.get(id) : null;
                if (oldEditor) {
                    oldEditor.remove();
                }

                console.log('TINYMCE IMAGE CONFIG', { image_caption: true });

                tinymce.baseURL = 'https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2';
                tinymce.suffix = '.min';

                // Cất wrapper element để truyền vào ui_container dưới dạng DOM element
                // KHÔNG truyền CSS selector string — dễ bị null trong Filament Tabs race condition
                const _uiContainer = this.$el;

                tinymce.init({
                    target: this.$refs.editor,
                    base_url: 'https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2',
                    suffix: '.min',
                    language: 'vi',
                    language_url: '/js/tinymce/langs/vi.js',
                    // ui_container: giới hạn popup/menu/dialog trong wrapper thay vì body
                    // QUAN TRỌNG: truyền DOM element trực tiếp (không phải selector string)
                    // Điều này đảm bảo TinyMCE luôn có container hợp lệ dù Filament Tabs render lúc nào.
                    ui_container: _uiContainer,
                    height: 900,
                    min_height: 800,
                    skin: 'oxide',
                    skin_url: 'https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/skins/ui/oxide',
                    content_css: 'https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.2/skins/content/default/content.min.css',
                    branding: false,
                    promotion: false,
                    convert_urls: false,
                    relative_urls: false,
                    remove_script_host: false,
                    menubar: 'file edit view insert format tools table',
                    plugins: 'advlist autolink lists link image charmap preview anchor searchreplace visualblocks code fullscreen insertdatetime media table help wordcount directionality emoticons codesample',
                    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | blockquote | link image media table | removeformat searchreplace code preview fullscreen',
                    // toolbar_sticky: false — Tắt hoàn toàn sticky toolbar
                    // Lý do: TinyMCE 6 sticky dùng position:fixed và tính left từ getBoundingClientRect()
                    // Trong Filament Tabs (race condition), getBoundingClientRect().left = 0
                    // → toolbar bị fix tại cạnh trái viewport, đè lên sidebar ngẫu nhiên
                    toolbar_sticky: false,
                    image_caption: true,
                    extended_valid_elements: 'figure[*],figcaption[contenteditable|class|style|*],img[*]',
                    valid_children: '+figure[img|figcaption],+body[figure]',
                    content_style: 'body { font-family: Arial, sans-serif; font-size: 16px; line-height: 1.7; color: #334155; width: 100%; max-width: none; margin: 0; padding: 1.5rem; box-sizing: border-box; } img, figure, table, blockquote { max-width: 100%; } figure.image { text-align: center; margin: 1.5rem 0; } figure.image img { border-radius: 0.75rem; max-width: 100%; height: auto; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1); } figure.image figcaption { margin-top: 5px; font-size: 0.9em; color: #666; font-style: italic; cursor: text; user-select: text; -webkit-user-select: text; }',
                    
                    // Admin image upload integration
                    images_upload_url: this.uploadUrl,
                    images_upload_credentials: true,
                    automatic_uploads: true,
                    images_reuse_filename: false,
                    paste_data_images: false,
                    images_upload_handler: (blobInfo, progress) => {
                        return new Promise((resolve, reject) => {
                            let xhr = new XMLHttpRequest();
                            xhr.withCredentials = true;
                            xhr.open('POST', this.uploadUrl);
                            xhr.setRequestHeader('X-CSRF-TOKEN', this.csrfToken);

                            xhr.upload.onprogress = (e) => {
                                progress(e.loaded / e.total * 100);
                            };

                            xhr.onload = () => {
                                if (xhr.status === 403) {
                                    reject({ message: 'Lỗi 403: Không có quyền truy cập', remove: true });
                                    return;
                                }
                                if (xhr.status <= 199 || xhr.status >= 300) {
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

                        // Intercept clicks on figcaption to manually focus and select its content for editing
                        editor.on('click', (e) => {
                            let figcaption = editor.dom.getParent(e.target, 'figcaption');
                            if (figcaption) {
                                figcaption.setAttribute('contenteditable', 'true');
                                editor.selection.select(figcaption, true);
                            }
                            // Force focus directly into the editor when clicking inside the iframe (breaks focus traps)
                            editor.focus();
                        });

                        // Intercept default mceLink command execution to show custom dialog
                        console.log('CUSTOM MCE LINK REGISTERED');
                        editor.on('BeforeExecCommand', (e) => {
                            if (e.command === 'mceLink') {
                                e.preventDefault();
                                console.log('CUSTOM MCE LINK EXECUTED');
                                let selectedNode = editor.selection.getNode();
                                let urlVal = '';
                                let textVal = '';
                                let targetVal = false;
                                let relNofollowVal = false;
                                let isLink = false;

                                let anchorNode = editor.dom.getParent(selectedNode, 'a');
                                if (anchorNode) {
                                    urlVal = anchorNode.getAttribute('href') || '';
                                    textVal = anchorNode.innerText || anchorNode.textContent || '';
                                    targetVal = anchorNode.getAttribute('target') === '_blank';
                                    relNofollowVal = (anchorNode.getAttribute('rel') || '').includes('nofollow');
                                    isLink = true;
                                } else {
                                    textVal = editor.selection.getContent({ format: 'text' }) || '';
                                }

                                let excludeId = config.excludeId || '';
                                let searchUrl = config.searchUrl;
                                console.log('SEARCH URL', searchUrl);

                                let dialogApi = editor.windowManager.open({
                                    title: 'Chèn/Sửa liên kết',
                                    body: {
                                        type: 'panel',
                                        items: [
                                            {
                                                type: 'input',
                                                name: 'url',
                                                label: 'Đường dẫn (URL)',
                                                placeholder: 'Nhập địa chỉ URL hoặc chọn bài viết bên dưới...'
                                            },
                                            {
                                                type: 'input',
                                                name: 'text',
                                                label: 'Văn bản hiển thị',
                                                placeholder: 'Văn bản hiển thị liên kết...'
                                            },
                                            {
                                                type: 'input',
                                                name: 'search_article',
                                                label: 'Tìm bài viết nội bộ',
                                                placeholder: 'Nhập tiêu đề hoặc đường dẫn để tìm...'
                                            },
                                            {
                                                type: 'htmlpanel',
                                                html: `\x3cdiv id="tinymce-article-search-results" role="listbox" style="max-height: 180px; overflow-y: auto; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 4px; background: #fff; display: none;"\x3e\x3c/div\x3e`
                                            },
                                            {
                                                type: 'checkbox',
                                                name: 'target',
                                                label: 'Mở liên kết trong thẻ mới (_blank)'
                                            },
                                            {
                                                type: 'checkbox',
                                                name: 'nofollow',
                                                label: 'Thêm thuộc tính rel="nofollow" (Khuyên dùng cho liên kết ngoài)'
                                            }
                                        ]
                                    },
                                    buttons: [
                                        { type: 'cancel', text: 'Hủy' },
                                        { type: 'submit', text: 'Đồng ý', primary: true }
                                    ],
                                    onSubmit: (api) => {
                                        const data = api.getData();
                                        let href = data.url.trim();
                                        let text = data.text.trim() || href;

                                        if (!href) {
                                            alert('Đường dẫn (URL) không được để trống.');
                                            return;
                                        }

                                        let targetAttr = data.target ? '_blank' : null;
                                        let relAttr = data.nofollow ? 'nofollow' : null;

                                        if (isLink && anchorNode) {
                                            anchorNode.setAttribute('href', href);
                                            anchorNode.textContent = text;
                                            if (targetAttr) {
                                                anchorNode.setAttribute('target', targetAttr);
                                            } else {
                                                anchorNode.removeAttribute('target');
                                            }
                                            if (relAttr) {
                                                anchorNode.setAttribute('rel', relAttr);
                                            } else {
                                                anchorNode.removeAttribute('rel');
                                            }
                                        } else {
                                            let link = document.createElement('a');
                                            link.setAttribute('href', href);
                                            link.textContent = text;
                                            if (targetAttr) {
                                                link.setAttribute('target', targetAttr);
                                            }
                                            if (relAttr) {
                                                link.setAttribute('rel', relAttr);
                                            }
                                            editor.insertContent(link.outerHTML);
                                        }
                                        api.close();
                                    }
                                });

                                dialogApi.setData({
                                    url: urlVal,
                                    text: textVal,
                                    target: targetVal,
                                    nofollow: relNofollowVal
                                });

                                console.log('CUSTOM LINK DIALOG OPENED');

                                // Setup search-as-you-type and keyboard navigation
                                setTimeout(() => {
                                    const dialogEl = document.querySelector('.tox-dialog');
                                    if (!dialogEl) return;

                                    const searchInput = dialogEl.querySelector('input[placeholder="Nhập tiêu đề hoặc đường dẫn để tìm..."]');
                                    const resultsDiv = dialogEl.querySelector('#tinymce-article-search-results');

                                    if (!searchInput || !resultsDiv) return;

                                    let activeAbortController = null;
                                    let debounceTimeout = null;
                                    let selectedIndex = -1;

                                    // Helper to escape HTML to prevent XSS
                                    function escapeHtml(text) {
                                        if (!text) return '';
                                        return text
                                            .replace(/&/g, '&amp;')
                                            .replace(/</g, '&lt;')
                                            .replace(/>/g, '&gt;')
                                            .replace(/"/g, '&quot;')
                                            .replace(/'/g, '&#039;');
                                    }

                                    // Helper to highlight search keywords safely
                                    function highlightText(text, query) {
                                        if (!query) return text;
                                        const escapedQuery = query.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                                        const regex = new RegExp(`(${escapedQuery})`, 'gi');
                                        return text.replace(regex, '\x3cmark style="background-color: #fef08a; color: #0f172a; padding: 0 2px; border-radius: 2px;"\x3e$1\x3c/mark\x3e');
                                    }

                                    // Helper to highlight current selection in list box
                                    function highlightListItem(index) {
                                        const items = resultsDiv.querySelectorAll('.tinymce-search-item');
                                        items.forEach((item, i) => {
                                            if (i === index) {
                                                item.style.backgroundColor = '#e2e8f0';
                                                item.setAttribute('aria-selected', 'true');
                                                item.scrollIntoView({ block: 'nearest' });
                                            } else {
                                                item.style.backgroundColor = 'transparent';
                                                item.setAttribute('aria-selected', 'false');
                                            }
                                        });
                                    }

                                    // Render links list helper
                                    function renderLinksList(links, isSearch = false, query = '') {
                                        if (links.length === 0) {
                                            resultsDiv.innerHTML = '\x3cdiv style="padding: 10px; text-align: center; color: #64748b;"\x3eKhông tìm thấy kết quả\x3c/div\x3e';
                                            return;
                                        }

                                        let html = '';
                                        const typeLabels = {
                                            'article': 'Bài viết',
                                            'category': 'Danh mục',
                                            'doctor': 'Bác sĩ',
                                            'service': 'Dịch vụ',
                                            'page': 'Trang tĩnh'
                                        };

                                        links.forEach((link, idx) => {
                                            const typeLabel = typeLabels[link.type] || link.type || 'Nội dung';
                                            const escapedTitle = escapeHtml(link.title);
                                            const displayTitle = isSearch ? highlightText(escapedTitle, query) : escapedTitle;
                                            const displayUrl = escapeHtml(link.url);

                                            html += `
                                                \x3cdiv class="tinymce-search-item" 
                                                     role="option"
                                                     aria-selected="false"
                                                     data-url="${escapeHtml(link.url)}" 
                                                     data-title="${escapedTitle.replace(/"/g, '&quot;')}"
                                                     style="padding: 8px 12px; cursor: pointer; border-bottom: 1px solid #f1f5f9; transition: background 0.2s;"
                                                     onmouseover="this.style.backgroundColor='#f8fafc'"
                                                     onmouseout="this.style.backgroundColor='transparent'"\x3e
                                                    \x3cdiv style="font-weight: 600; color: #1e293b; font-size: 14px;"\x3e${displayTitle}\x3c/div\x3e
                                                    \x3cdiv style="display: flex; justify-content: space-between; align-items: center; margin-top: 2px;"\x3e
                                                        \x3cspan style="color: #64748b; font-size: 12px;"\x3e${displayUrl}\x3c/span\x3e
                                                        \x3cspan style="background: #e0f2fe; color: #0369a1; font-size: 11px; padding: 2px 6px; border-radius: 4px; font-weight: 500;"\x3e${typeLabel}\x3c/span\x3e
                                                    \x3c/div\x3e
                                                \x3c/div\x3e
                                            `;
                                        });

                                        resultsDiv.innerHTML = html;
                                        selectedIndex = -1;

                                        // Bind click event to selection
                                        const items = resultsDiv.querySelectorAll('.tinymce-search-item');
                                        items.forEach(item => {
                                            item.addEventListener('click', () => {
                                                const url = item.getAttribute('data-url');
                                                const title = item.getAttribute('data-title');
                                                const currentData = dialogApi.getData();

                                                dialogApi.setData({
                                                    url: url,
                                                    text: currentData.text.trim() === '' ? title : currentData.text,
                                                    search_article: ''
                                                });

                                                // Hide search list
                                                resultsDiv.style.display = 'none';
                                                resultsDiv.innerHTML = '';
                                            });
                                        });
                                    }

                                    // Load and render Recent Articles helper
                                    function loadRecentArticles() {
                                        // Check memory cache first
                                        const cached = window.tinymceRecentLinksCache;
                                        if (cached && cached.data && (Date.now() - cached.timestamp <= 299999)) {
                                            resultsDiv.style.display = 'block';
                                            renderLinksList(cached.data, false);
                                            return;
                                        }

                                        resultsDiv.style.display = 'block';
                                        resultsDiv.innerHTML = '\x3cdiv style="padding: 10px; text-align: center; color: #64748b;"\x3eĐang tải bài viết gần đây...\x3c/div\x3e';

                                        fetch(`${searchUrl}?exclude_id=${excludeId}`, {
                                            headers: {
                                                'Accept': 'application/json',
                                                'X-Requested-With': 'XMLHttpRequest'
                                            }
                                        })
                                            .then(res => {
                                                if (!res.ok) {
                                                    if (res.status === 401) throw new Error('Chưa đăng nhập (401)');
                                                    if (res.status === 403) throw new Error('Không có quyền truy cập (403)');
                                                    if (res.status === 404) throw new Error('Không tìm thấy API (404)');
                                                    if (res.status >= 500) throw new Error('Lỗi máy chủ (' + res.status + ')');
                                                    throw new Error('Lỗi phản hồi (' + res.status + ')');
                                                }
                                                return res.json();
                                            })
                                            .then(data => {
                                                // Save to cache
                                                window.tinymceRecentLinksCache = {
                                                    data: data,
                                                    timestamp: Date.now()
                                                };
                                                renderLinksList(data, false);
                                            })
                                            .catch(err => {
                                                console.error(err);
                                                let errMsg = 'Không thể kết nối máy chủ';
                                                if (err.message) {
                                                    errMsg = err.message;
                                                }
                                                resultsDiv.innerHTML = `\x3cdiv style="padding: 10px; text-align: center; color: #ef4444;"\x3e${escapeHtml(errMsg)}\x3c/div\x3e`;
                                            });
                                    }

                                    // 1. Load initial Recent Articles
                                    loadRecentArticles();

                                    // 2. Listen to input events
                                    searchInput.addEventListener('input', (e) => {
                                        const query = e.target.value.trim();
                                        clearTimeout(debounceTimeout);

                                        // Cancel any in-flight requests
                                        if (activeAbortController) {
                                            activeAbortController.abort();
                                            activeAbortController = null;
                                        }

                                        if (query === '') {
                                            loadRecentArticles();
                                            return;
                                        }

                                        if (query.length <= 1) {
                                            resultsDiv.style.display = 'none';
                                            resultsDiv.innerHTML = '';
                                            return;
                                        }

                                        resultsDiv.style.display = 'block';
                                        resultsDiv.innerHTML = '\x3cdiv style="padding: 10px; text-align: center; color: #64748b;"\x3eĐang tìm kiếm...\x3c/div\x3e';

                                        debounceTimeout = setTimeout(() => {
                                            activeAbortController = new AbortController();

                                            fetch(`${searchUrl}?q=${encodeURIComponent(query)}&exclude_id=${excludeId}`, {
                                                signal: activeAbortController.signal,
                                                headers: {
                                                    'Accept': 'application/json',
                                                    'X-Requested-With': 'XMLHttpRequest'
                                                }
                                            })
                                                .then(res => {
                                                    if (!res.ok) {
                                                        if (res.status === 401) throw new Error('Chưa đăng nhập (401)');
                                                        if (res.status === 403) throw new Error('Không có quyền truy cập (403)');
                                                        if (res.status === 404) throw new Error('Không tìm thấy API (404)');
                                                        if (res.status >= 500) throw new Error('Lỗi máy chủ (' + res.status + ')');
                                                        throw new Error('Lỗi phản hồi (' + res.status + ')');
                                                    }
                                                    return res.json();
                                                })
                                                .then(data => {
                                                    renderLinksList(data, true, query);
                                                })
                                                .catch(err => {
                                                    if (err.name === 'AbortError') return;
                                                    console.error(err);
                                                    let errMsg = 'Không thể kết nối máy chủ';
                                                    if (err.message) {
                                                        errMsg = err.message;
                                                    }
                                                    resultsDiv.innerHTML = `\x3cdiv style="padding: 10px; text-align: center; color: #ef4444;"\x3e${escapeHtml(errMsg)}\x3c/div\x3e`;
                                                });
                                        }, 300);
                                    });

                                    // 3. Keydown navigation listener
                                    searchInput.addEventListener('keydown', (e) => {
                                        const items = resultsDiv.querySelectorAll('.tinymce-search-item');
                                        if (items.length === 0) return;

                                        if (e.key === 'ArrowDown') {
                                            e.preventDefault();
                                            selectedIndex++;
                                            if (selectedIndex >= items.length) selectedIndex = 0;
                                            highlightListItem(selectedIndex);
                                        } else if (e.key === 'ArrowUp') {
                                            e.preventDefault();
                                            selectedIndex--;
                                            if (selectedIndex <= -1) selectedIndex = items.length - 1;
                                            highlightListItem(selectedIndex);
                                        } else if (e.key === 'Enter') {
                                            if (selectedIndex >= 0 && selectedIndex <= items.length - 1) {
                                                e.preventDefault();
                                                e.stopPropagation();
                                                items[selectedIndex].click();
                                            }
                                        } else if (e.key === 'Escape') {
                                            e.preventDefault();
                                            resultsDiv.style.display = 'none';
                                            resultsDiv.innerHTML = '';
                                        }
                                    });
                                }, 50);
                            }
                        });

                        // Helper: apply content and force editable design mode
                    const ensureEditorReady = () => {
                        try {
                            if (editor.mode && typeof editor.mode.get === 'function' && editor.mode.get() !== 'design') {
                                editor.mode.set('design');
                            }
                            let body = editor.getBody();
                            if (body && body.getAttribute('contenteditable') !== 'true') {
                                body.setAttribute('contenteditable', 'true');
                            }
                        } catch (e) {}
                    };

                    editor.on('init', () => {
                        // Use the exact same activation sequence as switchTab('visual').
                        // TinyMCE's editor.show() internally calls editorManager.setActive(this)
                        // which registers the editor as the active, interactive instance.
                        // Without calling show(), the editor renders visually but never becomes
                        // interactive (no keyboard/mouse input accepted).
                        //
                        // Strategy for initial content (most authoritative source first):
                        //   1. this.state — already hydrated by Livewire entangle
                        //   2. this._pendingContent — captured by $watch before editor was ready
                        //   3. this.$wire.get(statePath) — direct Livewire fetch as final fallback

                        const activateEditor = (content) => {
                            if (this.activeTab === 'visual') {
                                // Step 1: hide() then show() with a brief gap.
                                // This is the CRITICAL step — show() triggers setActive() inside TinyMCE.
                                editor.hide();
                                setTimeout(() => {
                                    editor.show();
                                    // Step 2: Set content (always, even empty — activates iframe body)
                                    editor.setContent(content || '');
                                    // Step 3: Unconditionally force design mode + contenteditable
                                    try {
                                        if (editor.mode && typeof editor.mode.get === 'function') {
                                            editor.mode.set('design');
                                        }
                                        let body = editor.getBody();
                                        if (body) {
                                            body.setAttribute('contenteditable', 'true');
                                        }
                                    } catch (e) {}
                                    // Step 4: Focus the editor
                                    setTimeout(() => { editor.focus(); }, 50);
                                    this.editorReady = true;
                                }, 50);
                            } else {
                                editor.hide();
                                this.editorReady = true;
                            }
                        };
                        // Determine initial content and activate
                        let immediateContent = '';
                        try {
                            immediateContent = this.state || this._pendingContent || this.resolvePath(this.$wire, this.statePath) || '';
                        } catch (e) {
                            console.warn('TinyMCE resolve initial content error:', e);
                            immediateContent = this.state || this._pendingContent || '';
                        }
                        activateEditor(immediateContent);
                        this.state = immediateContent;
                        this._pendingContent = null;
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

                    // Sync before Livewire handles submit - uses CAPTURE phase to execute BEFORE Livewire's bubble phase submit handler
                    this._formSubmitHandler = () => {
                        if (!this.editorReady || !this.editorInstanceId) {
                            alert('DEBUG: TinyMCE not ready. editorReady=' + this.editorReady + ', ID=' + this.editorInstanceId);
                            return;
                        }
                        try {
                            let editor = tinymce.get(this.editorInstanceId);
                            if (!editor) {
                                alert('DEBUG: TinyMCE editor instance not found for ID=' + this.editorInstanceId);
                                return;
                            }
                            const content = (this.activeTab === 'visual')
                                ? editor.getContent()
                                : (this.$refs.editor ? this.$refs.editor.value : this.state);
                            
                            alert('DEBUG: Syncing content on submit. Length: ' + content.length + ', preview: ' + content.substring(0, 80));
                            
                            this.state = content;
                            this.$wire.set(this.statePath, content, false);
                        } catch (e) {
                            alert('DEBUG: Sync error: ' + e.message);
                        }
                    };

                    this.$nextTick(() => {
                        let form = this.$el.closest('form');
                        if (form) {
                            if (this._formElement && this._formSubmitHandler) {
                                try {
                                    this._formElement.removeEventListener('submit', this._formSubmitHandler, { capture: true });
                                } catch (e) {}
                            }
                            this._formElement = form;
                            form.addEventListener('submit', this._formSubmitHandler, { capture: true });
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
                        if (editor.mode && typeof editor.mode.get === 'function' && editor.mode.get() !== 'design') {
                            editor.mode.set('design');
                        }
                        let body = editor.getBody();
                        if (body && body.getAttribute('contenteditable') !== 'true') {
                            body.setAttribute('contenteditable', 'true');
                        }
                    } catch (e) {}
                    setTimeout(() => {
                        editor.focus();
                    }, 100);
                }
                this.activeTab = 'visual';
            }
        },
        
        destroy() {
            let id = this.editorInstanceId || (this.$refs.editor ? this.$refs.editor.id : null);
            console.log('TINYMCE DESTROY', id);
            if (this._visibilityObserver) {
                this._visibilityObserver.disconnect();
                this._visibilityObserver = null;
            }
            // Clean up TinyMCE instance when leaving the page (essential for Filament SPA navigation)
            if (this._syncAbortController) {
                this._syncAbortController.abort();
                this._syncAbortController = null;
            }
            // Gỡ bỏ Livewire commit hook để tránh memory leak
            if (typeof this._livewireCommitHook === 'function') {
                try { this._livewireCommitHook(); } catch (e) {}
                this._livewireCommitHook = null;
            }
            // Gỡ bỏ force-sync event listener
            if (typeof this._forceSyncHandler === 'function') {
                window.removeEventListener('tinymce-force-sync-before-save', this._forceSyncHandler);
                this._forceSyncHandler = null;
            }
            if (this._formElement && this._formSubmitHandler) {
                this._formElement.removeEventListener('submit', this._formSubmitHandler, { capture: true });
                this._formElement = null;
                this._formSubmitHandler = null;
            }
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
        },

        resolvePath(obj, path) {
            if (!obj || !path || typeof path !== 'string') return undefined;
            try {
                return path.split('.').reduce((acc, part) => acc && acc[part], obj);
            } catch (e) {
                console.warn('TinyMCE resolvePath error:', e);
                return undefined;
            }
        }
    }));
}

if (typeof Alpine !== 'undefined') {
    registerTinyMceEditor();
}
document.addEventListener('alpine:init', registerTinyMceEditor);
})();
</script>

{{-- ============================================================
     TinyMCE + Filament Admin — Toolbar Sticky Fix
     
     NGUYÊN TẬ:
     • ui_container phải được giữ nguyên — điều hướng popup/menu vào wrapper
     • toolbar_sticky_offset được tự đo từ DOM thực tế thay vì hardcode
     • KHÔNG override position:sticky vào .tox-editor-header qua CSS
     • KHÔNG dùng contain:layout trên wrapper
     ============================================================ --}}
<style>
    /*
     * Toolbar responsive: wrap buttons thay vì bị cắt khi màn hình hẹp
     */
    .tox .tox-toolbar__primary {
        flex-wrap: wrap !important;
    }

    /*
     * Editor fill 100% width của wrapper
     */
    .tox-tinymce {
        width: 100% !important;
    }

    /*
     * Border radius đáy editor khớp với wrapper border-radius
     */
    .tinymce-editor-container .tox-tinymce {
        border-radius: 0 0 0.5rem 0.5rem;
        border-top: none;
    }
</style>
</div>
