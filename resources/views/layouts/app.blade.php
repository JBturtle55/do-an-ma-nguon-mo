<!DOCTYPE html>
<html lang="vi" x-data="{ sidebarOpen: false }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name')) — {{ config('app.name') }}</title>
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-gray-50 font-sans antialiased">
    <div class="flex h-screen overflow-hidden">
        <x-sidebar />
        <div x-show="sidebarOpen" @click="sidebarOpen = false"
             class="fixed inset-0 bg-black/50 z-20 lg:hidden" style="display:none"></div>
        <div class="flex flex-col flex-1 overflow-y-auto">
            <header class="bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between sticky top-0 z-10 shadow-sm">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-500 hover:text-gray-700">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                    <span class="font-semibold text-gray-800">@yield('title', 'Dashboard')</span>
                </div>
                <div class="flex items-center gap-3">
                    {{-- Notification Bell with real-time polling --}}
                    <div x-data="notificationBell()" class="relative">
                        <button @click="open = !open; if(open) fetchNotifications()"
                                class="relative text-gray-500 hover:text-gray-700 focus:outline-none">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span x-show="count > 0" x-text="count > 9 ? '9+' : count"
                                  style="display:none; position:absolute; top:-4px; right:-4px; background:#ef4444; color:#fff; font-size:10px; font-weight:600; min-width:16px; height:16px; border-radius:9999px; padding:0 3px; text-align:center; line-height:16px; box-sizing:border-box;"></span>
                        </button>

                        {{-- Dropdown --}}
                        <div x-show="open" @click.outside="open = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                             class="absolute right-0 mt-2 bg-white rounded-xl shadow-lg border border-gray-100 z-50"
                             style="display:none; width:320px; min-width:320px;">
                            <div class="px-4 py-2 border-b border-gray-100 flex items-center justify-between">
                                <span class="text-sm font-semibold text-gray-700">Thông báo</span>
                                <button x-show="items.length > 0" @click="markAllRead()"
                                        class="text-xs text-blue-600 hover:underline" style="display:none">Đọc tất cả</button>
                            </div>
                            <div style="max-height:320px; overflow-y:auto;">
                                <p x-show="items.length === 0"
                                   class="px-4 py-6 text-sm text-gray-400 text-center">Không có thông báo mới</p>
                                <template x-for="item in items" :key="item.id">
                                    <div class="flex items-start gap-3 px-4 py-3 border-b border-gray-50 last:border-0 hover:bg-gray-50">
                                        <span style="width:8px; height:8px; border-radius:50%; background:#3b82f6; flex-shrink:0; margin-top:5px;"></span>
                                        <div style="flex:1; min-width:0;">
                                            <p class="text-sm text-gray-700 leading-snug" x-text="item.message"></p>
                                            <span class="text-xs text-gray-400 mt-1 block" x-text="item.time"></span>
                                        </div>
                                        <button @click.stop="markRead(item.id)"
                                                title="Đánh dấu đã đọc"
                                                style="flex-shrink:0; color:#d1d5db; padding:2px; line-height:1;"
                                                onmouseover="this.style.color='#6b7280'" onmouseout="this.style.color='#d1d5db'">
                                            <svg style="width:14px;height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" class="flex items-center gap-2 text-sm text-gray-700 hover:text-gray-900">
                            <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white font-semibold text-xs">
                                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                            </div>
                            <span class="hidden sm:block font-medium">{{ auth()->user()->name }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="open" @click.outside="open = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50" style="display:none">
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Hồ sơ cá nhân</a>
                            <hr class="my-1 border-gray-100">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">Đăng xuất</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>
            <main class="flex-1 p-4 sm:p-6">
                <x-alert />
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')

    {{-- AI Chat Widget --}}
    <div x-data="aiChat()" class="fixed bottom-6 right-6 z-40">
        {{-- Chat panel --}}
        <div x-show="open"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95 translate-y-2"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-2"
             class="absolute bottom-16 right-0 bg-white rounded-xl shadow-2xl border border-gray-200 flex flex-col overflow-hidden"
             style="width:320px;height:430px;display:none">
            {{-- Header --}}
            <div class="px-4 py-3 bg-blue-600 flex items-center gap-2 flex-shrink-0">
                <div class="w-7 h-7 bg-blue-500 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2h-2"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="text-white text-sm font-semibold leading-tight">Trợ lý AI</p>
                    <p class="text-blue-200 text-xs">Hỏi về phòng, booking, thiết bị...</p>
                </div>
                <button @click="open = false" class="text-blue-200 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Messages --}}
            <div class="flex-1 overflow-y-auto p-3 space-y-2 bg-gray-50" x-ref="msgArea">
                <template x-if="messages.length === 0">
                    <div class="text-center pt-6">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                            <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3v-3z"/>
                            </svg>
                        </div>
                        <p class="text-xs text-gray-400">Xin chào! Tôi có thể giúp bạn tra cứu phòng trống, tình trạng booking, hoặc thông tin thiết bị.</p>
                    </div>
                </template>
                <template x-for="msg in messages" :key="msg.id">
                    <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                        <div :class="msg.role === 'user'
                                ? 'bg-blue-600 text-white rounded-tl-xl rounded-tr-sm rounded-bl-xl'
                                : 'bg-white text-gray-800 rounded-tr-xl rounded-tl-sm rounded-br-xl border border-gray-100 shadow-sm'"
                             class="px-3 py-2 text-sm max-w-[85%] leading-relaxed"
                             x-text="msg.content"></div>
                    </div>
                </template>
                <template x-if="loading">
                    <div class="flex justify-start">
                        <div class="bg-white border border-gray-100 rounded-xl px-3 py-2 shadow-sm">
                            <span class="text-gray-400 text-xs">Đang trả lời</span>
                            <span class="inline-flex gap-0.5 ml-1">
                                <span class="w-1 h-1 bg-gray-400 rounded-full animate-bounce" style="animation-delay:0ms"></span>
                                <span class="w-1 h-1 bg-gray-400 rounded-full animate-bounce" style="animation-delay:150ms"></span>
                                <span class="w-1 h-1 bg-gray-400 rounded-full animate-bounce" style="animation-delay:300ms"></span>
                            </span>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Input --}}
            <div class="p-3 bg-white border-t border-gray-100 flex-shrink-0">
                <form @submit.prevent="send()" class="flex gap-2">
                    <input x-model="input"
                           :disabled="loading"
                           @keydown.enter.prevent="send()"
                           type="text"
                           placeholder="Nhập câu hỏi..."
                           class="flex-1 text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:border-blue-400 disabled:bg-gray-50">
                    <button type="submit"
                            :disabled="loading || !input.trim()"
                            class="bg-blue-600 hover:bg-blue-700 text-white rounded-lg px-3 py-2 transition-colors disabled:opacity-40">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                    </button>
                </form>
            </div>
        </div>

        {{-- Toggle button --}}
        <button @click="open = !open"
                class="w-13 h-13 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg flex items-center justify-center transition-all hover:scale-105"
                style="width:52px;height:52px">
            <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-3 3v-3z"/>
            </svg>
            <svg x-show="open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <script>
    function notificationBell() {
        return {
            count: @json(auth()->user()->unreadNotifications()->count()),
            items: [],
            open: false,

            init() {
                this.poll();
                setInterval(() => this.poll(), 30000);
            },

            async poll() {
                try {
                    const res = await fetch('{{ route('notifications.unread-count') }}', {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    this.count = data.count;
                    if (this.open) this.items = data.items;
                } catch {}
            },

            async fetchNotifications() {
                try {
                    const res = await fetch('{{ route('notifications.unread-count') }}', {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });
                    if (!res.ok) return;
                    const data = await res.json();
                    this.count = data.count;
                    this.items = data.items;
                } catch {}
            },

            async markRead(id) {
                try {
                    await fetch(`/notifications/${id}/read`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    });
                    this.items = this.items.filter(i => i.id !== id);
                    this.count = Math.max(0, this.count - 1);
                } catch {}
            },

            async markAllRead() {
                try {
                    await fetch('{{ route('notifications.read-all') }}', {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                        }
                    });
                    this.items = [];
                    this.count = 0;
                } catch {}
            },
        };
    }

    function aiChat() {
        return {
            open: false,
            input: '',
            messages: [],
            loading: false,

            async send() {
                const text = this.input.trim();
                if (!text || this.loading) return;
                this.input = '';
                this.messages.push({ id: Date.now(), role: 'user', content: text });
                this.loading = true;
                this.$nextTick(() => this.scrollToBottom());
                try {
                    const res = await fetch('{{ route('chat.send') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            message: text,
                            history: this.messages.slice(-8),
                        }),
                    });
                    const data = await res.json();
                    this.messages.push({ id: Date.now() + 1, role: 'assistant', content: data.reply || 'Không có phản hồi.' });
                } catch {
                    this.messages.push({ id: Date.now() + 1, role: 'assistant', content: 'Lỗi kết nối. Thử lại sau.' });
                }
                this.loading = false;
                this.$nextTick(() => this.scrollToBottom());
            },

            scrollToBottom() {
                const el = this.$refs.msgArea;
                if (el) el.scrollTop = el.scrollHeight;
            },
        };
    }
    </script>
</body>
</html>
