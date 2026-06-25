@extends('layouts.app')
@section('title', 'Bảo trì & Sự cố')
@section('content')
<div class="space-y-4" x-data="{
    confirmOpen: false,
    confirmUrl: '',
    confirmTitle: '',
    confirmMessage: '',
    confirmBtnLabel: '',
    confirmColor: 'blue',
    openConfirm(url, title, message, btnLabel, color) {
        this.confirmUrl    = url;
        this.confirmTitle  = title;
        this.confirmMessage = message;
        this.confirmBtnLabel = btnLabel;
        this.confirmColor  = color || 'blue';
        this.confirmOpen   = true;
    }
}" @keydown.escape.window="confirmOpen = false">

    <div class="flex justify-between items-center">
        <h1 class="text-xl font-bold text-gray-800">Bảo trì & Sự cố</h1>
        <a href="{{ route('admin.maintenance.create') }}" class="btn-primary">+ Báo cáo sự cố</a>
    </div>

    <div class="card">
        <form method="GET" class="flex gap-3">
            <select name="status" class="form-input w-40">
                <option value="">Tất cả</option>
                <option value="open" @selected(request('status') === 'open')>Mới báo</option>
                <option value="in_progress" @selected(request('status') === 'in_progress')>Đang xử lý</option>
                <option value="resolved" @selected(request('status') === 'resolved')>Đã giải quyết</option>
            </select>
            <button type="submit" class="btn-secondary">Lọc</button>
        </form>
    </div>

    <div class="card p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="table-th">Đối tượng</th>
                    <th class="table-th">Mô tả</th>
                    <th class="table-th">Người báo</th>
                    <th class="table-th">Trạng thái</th>
                    <th class="table-th">Ngày báo</th>
                    <th class="table-th">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50">
                    <td class="table-td font-medium">{{ $log->loggable?->name ?? '—' }}</td>
                    <td class="table-td text-gray-600 max-w-xs">
                        <a href="{{ route('admin.maintenance.show', $log) }}" class="hover:underline text-gray-700 line-clamp-1">{{ $log->description }}</a>
                    </td>
                    <td class="table-td text-gray-500">{{ $log->reporter->name }}</td>
                    <td class="table-td"><x-badge :status="$log->status"/></td>
                    <td class="table-td text-gray-500">{{ $log->created_at->format('d/m/Y') }}</td>
                    <td class="table-td space-x-2">
                        @if($log->status === 'open')
                        <button type="button" class="text-blue-600 text-xs hover:underline font-medium"
                                @click="openConfirm(
                                    '{{ route('admin.maintenance.progress', $log) }}',
                                    'Chuyển sang đang xử lý',
                                    'Xác nhận chuyển trạng thái của sự cố này sang đang xử lý?',
                                    'Xác nhận', 'blue'
                                )">Đang xử lý</button>
                        @endif
                        @if($log->status !== 'resolved')
                        <button type="button" class="text-green-600 text-xs hover:underline font-medium"
                                @click="openConfirm(
                                    '{{ route('admin.maintenance.resolve', $log) }}',
                                    'Đánh dấu đã giải quyết',
                                    'Xác nhận đánh dấu sự cố này là đã giải quyết xong? Phòng/thiết bị sẽ được khôi phục về trạng thái sẵn sàng.',
                                    'Đánh dấu xong', 'green'
                                )">Đánh dấu xong</button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-8 text-gray-400">Không có sự cố nào.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($logs->hasPages())<div class="px-4 py-3 border-t">{{ $logs->links() }}</div>@endif
    </div>

    {{-- Confirm modal --}}
    <div x-show="confirmOpen" @click="confirmOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
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
