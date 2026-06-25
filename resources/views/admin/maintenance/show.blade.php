@extends('layouts.app')
@section('title', 'Chi tiết sự cố')
@section('content')
<div class="max-w-2xl mx-auto space-y-6" x-data="{
    confirmOpen: false,
    confirmUrl: '',
    confirmTitle: '',
    confirmMessage: '',
    confirmBtnLabel: '',
    confirmColor: 'blue',
    openConfirm(url, title, message, btnLabel, color) {
        this.confirmUrl = url;
        this.confirmTitle = title;
        this.confirmMessage = message;
        this.confirmBtnLabel = btnLabel;
        this.confirmColor = color || 'blue';
        this.confirmOpen = true;
    }
}" @keydown.escape.window="confirmOpen = false">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.maintenance.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Danh sách sự cố</a>
        <x-badge :status="$log->status"/>
    </div>

    <div class="card space-y-4">
        <h1 class="text-xl font-bold text-gray-800">Chi tiết sự cố</h1>

        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500 mb-1">Đối tượng</dt>
                <dd class="font-medium">{{ $log->loggable?->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 mb-1">Loại</dt>
                <dd class="font-medium">{{ class_basename($log->loggable_type) }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 mb-1">Người báo cáo</dt>
                <dd class="font-medium">{{ $log->reporter->name }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 mb-1">Ngày báo</dt>
                <dd class="font-medium">{{ $log->created_at->format('d/m/Y H:i') }}</dd>
            </div>
            @if($log->resolved_at)
            <div>
                <dt class="text-gray-500 mb-1">Ngày giải quyết</dt>
                <dd class="font-medium text-green-600">{{ $log->resolved_at->format('d/m/Y H:i') }}</dd>
            </div>
            @endif
        </dl>

        <div>
            <dt class="text-sm text-gray-500 mb-1">Mô tả sự cố</dt>
            <dd class="bg-gray-50 rounded-lg p-4 text-sm text-gray-800 leading-relaxed">{{ $log->description }}</dd>
        </div>

        @if($log->status !== 'resolved')
        <div class="flex gap-3 pt-2 border-t">
            @if($log->status === 'open')
            <button type="button" class="btn-secondary"
                    @click="openConfirm(
                        '{{ route('admin.maintenance.progress', $log) }}',
                        'Chuyển sang đang xử lý',
                        'Xác nhận chuyển trạng thái của sự cố này sang đang xử lý?',
                        'Xác nhận', 'blue'
                    )">Đang xử lý</button>
            @endif
            <button type="button" class="btn-primary"
                    @click="openConfirm(
                        '{{ route('admin.maintenance.resolve', $log) }}',
                        'Đánh dấu đã giải quyết',
                        'Xác nhận đánh dấu sự cố này là đã giải quyết xong? Phòng/thiết bị sẽ được khôi phục về trạng thái sẵn sàng.',
                        'Đánh dấu xong', 'green'
                    )">Đánh dấu xong</button>
        </div>
        @endif
    </div>

    {{-- Confirm modal --}}
    <div x-show="confirmOpen" @click="confirmOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/40 z-50" style="display:none"></div>
    <div x-show="confirmOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none" style="display:none">
        <div class="bg-white rounded-xl shadow-xl pointer-events-auto" style="width:340px;max-width:calc(100vw - 2rem)" @click.stop>
            <div class="flex items-center gap-2.5 px-4 pt-4 pb-3">
                <div class="w-7 h-7 rounded-full flex items-center justify-center flex-shrink-0"
                     :class="confirmColor === 'green' ? 'bg-green-100' : 'bg-blue-100'">
                    <template x-if="confirmColor === 'green'">
                        <svg class="w-3.5 h-3.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </template>
                    <template x-if="confirmColor !== 'green'">
                        <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </template>
                </div>
                <p class="font-semibold text-gray-900 text-sm flex-1" x-text="confirmTitle"></p>
                <button @click="confirmOpen = false" class="text-gray-300 hover:text-gray-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <p class="text-xs text-gray-500 px-4 pb-4 leading-relaxed" x-text="confirmMessage"></p>
            <div class="flex gap-2 justify-end px-4 pb-4">
                <button type="button" @click="confirmOpen = false" class="btn-secondary">Huỷ</button>
                <form :action="confirmUrl" method="POST" @submit="confirmOpen = false">
                    @csrf
                    <input type="hidden" name="_method" value="PATCH">
                    <button type="submit"
                            :class="confirmColor === 'green' ? 'btn-success' : 'btn-primary'"
                            x-text="confirmBtnLabel"></button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
