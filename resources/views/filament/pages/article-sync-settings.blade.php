<x-filament-panels::page>
    <div class="fi-api-sync-page space-y-6">
        @if(!$this->isConfirmed)
            <!-- SECURE PASSWORD CONFIRMATION SCREEN -->
            <div class="max-w-md mx-auto my-12">
                <div class="fi-lock-card relative overflow-hidden">
                    <!-- Top gradient accent -->
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-indigo-500 to-violet-500"></div>
                    
                    <div class="flex flex-col items-center text-center mt-4">
                        <!-- Key Lock Icon -->
                        <div class="w-16 h-16 bg-indigo-50 dark:bg-indigo-950/30 rounded-full flex items-center justify-center mb-5 border border-indigo-100 dark:border-indigo-900/30 shadow-inner relative" style="margin: 0 auto 1.25rem auto;">
                            <div class="absolute inset-0 bg-indigo-500/10 rounded-full blur-md"></div>
                            <svg class="w-8 h-8 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Xác thực Bảo mật</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-6 max-w-xs leading-relaxed">
                            Vui lòng xác nhận mật khẩu quản trị của bạn để mở khóa cấu hình API đồng bộ.
                        </p>
                    </div>

                    <form wire:submit.prevent="confirmPassword" class="space-y-4" x-data="{ show: false }">
                        <div>
                            <div class="fi-lock-input-wrapper">
                                <!-- Left Lock Icon -->
                                <span class="fi-lock-input-icon-left">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </span>

                                <!-- Input field -->
                                <input :type="show ? 'text' : 'password'" id="password" wire:model="password" class="fi-lock-input" placeholder="Nhập mật khẩu tài khoản..." required autocomplete="current-password">

                                <!-- Right Eye Toggle -->
                                <button type="button" @click="show = !show" class="fi-lock-input-icon-right">
                                    <!-- Eye open -->
                                    <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <!-- Eye closed -->
                                    <svg x-show="show" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.076m3.125-3.141A9.97 9.97 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21M9 17.508a3 3 0 01-3.708-3.7M12 12a3 3 0 00-3-3m5.079-1.079a3 3 0 00-3 3M3 3l18 18"/></svg>
                                </button>
                            </div>
                            
                            @error('password')
                                <span class="text-xs text-rose-500 mt-1 block font-medium text-center">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="fi-lock-btn">
                            Xác nhận & Mở khóa
                        </button>
                    </form>
                </div>
            </div>
        @else
            <!-- DEVELOPER CREDENTIALS CONSOLE -->
            <div class="space-y-6 max-w-4xl mx-auto">
                <!-- Info Banner -->
                <div class="fi-info-banner relative overflow-hidden text-white rounded-2xl p-6 shadow-lg">
                    <div class="absolute -right-16 -top-16 w-48 h-48 rounded-full bg-indigo-500/10 blur-2xl"></div>
                    <div class="relative flex items-start gap-4">
                        <div class="p-2.5 bg-white/10 rounded-xl border border-white/10 shadow-inner shrink-0">
                            <svg class="w-6 h-6 text-indigo-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-base text-white mb-1">Cấu hình Tự động Đồng bộ Bài viết</h4>
                            <p class="text-xs leading-relaxed max-w-2xl text-slate-200">
                                Cung cấp các thông tin Endpoint và Access Token dưới đây cấu hình vào mục đồng bộ của website nhận <strong>(Sống Khỏe Mỗi Ngày)</strong> để các bài viết được đẩy tự động khi xuất bản.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- API Credentials Terminal -->
                <div class="fi-terminal-card p-8 rounded-2xl shadow-xl relative overflow-hidden">
                    <div class="absolute -left-16 -bottom-16 w-64 h-64 rounded-full bg-emerald-500/5 blur-3xl pointer-events-none"></div>

                    <div class="fi-terminal-header flex items-center justify-between pb-6 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full bg-rose-500"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
                            <div class="w-3 h-3 rounded-full bg-emerald-500"></div>
                            <span class="text-xs font-mono text-gray-500 ml-2">api-credentials-terminal</span>
                        </div>
                        <div class="text-[10px] font-mono px-3 py-1 rounded-full bg-gray-800 text-emerald-400 border border-emerald-500/10">
                            SSL SECURED
                        </div>
                    </div>

                    <div class="space-y-6">
                        <!-- API URL Section -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400">Developer Endpoint</label>
                                <span class="px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-400 font-mono text-[10px] uppercase font-bold">POST METHOD</span>
                            </div>
                            <div class="fi-code-box flex items-center justify-between rounded-xl p-4 font-mono text-xs">
                                <span class="select-all break-all pr-4">{{ $this->apiUrl }}</span>
                                <button x-data="{ copied: false }" @click="navigator.clipboard.writeText('{{ $this->apiUrl }}'); copied = true; setTimeout(() => copied = false, 2000)" class="p-2 transition hover:bg-gray-800 rounded-lg shrink-0">
                                    <span x-show="!copied">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                    </span>
                                    <span x-show="copied" class="text-emerald-400 font-semibold flex items-center gap-1 text-[11px]" style="display: none;">
                                        <svg class="w-4 h-4 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Đã chép
                                    </span>
                                </button>
                            </div>
                        </div>

                        <!-- API Token Section -->
                        <div x-data="{ showToken: false }">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <label class="block text-xs font-semibold uppercase tracking-wider text-gray-400">Secret Access Token</label>
                                    <x-filament::button wire:click="regenerateToken" wire:confirm="Bạn có chắc chắn muốn tạo lại Token mới? Hành động này sẽ thay đổi token cũ và làm gián đoạn đồng bộ cho đến khi bạn cập nhật token mới ở website nhận." color="warning" size="xs" class="ml-2 font-semibold">
                                        Tạo lại Token mới
                                    </x-filament::button>
                                </div>
                                <button @click="showToken = !showToken" class="text-xs font-semibold text-gray-400 hover:text-indigo-400 transition flex items-center gap-1">
                                    <span x-show="!showToken">Hiển thị Token</span>
                                    <span x-show="showToken" style="display: none;">Ẩn Token</span>
                                </button>
                            </div>
                            <div class="fi-code-box flex items-center justify-between rounded-xl p-4 font-mono text-xs">
                                <span class="break-all select-all transition-all duration-300 pr-4" :class="showToken ? '' : 'blur-[4px] select-none'" x-text="showToken ? '{{ $this->syncToken }}' : '••••••••••••••••••••••••••••••••••••••••'"></span>
                                <button x-data="{ copied: false }" @click="navigator.clipboard.writeText('{{ $this->syncToken }}'); copied = true; setTimeout(() => copied = false, 2000)" class="p-2 transition hover:bg-gray-800 rounded-lg shrink-0">
                                    <span x-show="!copied">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                    </span>
                                    <span x-show="copied" class="text-emerald-400 font-semibold flex items-center gap-1 text-[11px]" style="display: none;">
                                        <svg class="w-4 h-4 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Đã chép
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
