@extends('layouts.app')
@section('title', 'Lịch cố định')
@section('content')
<div class="space-y-4" x-data="{
    confirmOpen: false,
    confirmUrl: '',
    openConfirm(url) { this.confirmUrl = url; this.confirmOpen = true; }
}" @keydown.escape.window="confirmOpen = false">
    <div class="flex justify-between items-center">
        <h1 class="text-xl font-bold text-gray-800">Lịch cố định</h1>
        <a href="{{ route('admin.schedules.create') }}" class="btn-primary">+ Thêm lịch</a>
    </div>
    <div class="card p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="table-th">Phòng</th>
                    <th class="table-th">Tiêu đề</th>
                    <th class="table-th">Loại lặp</th>
                    <th class="table-th">Thứ</th>
                    <th class="table-th">Giờ</th>
                    <th class="table-th">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @php $days = ['CN','T2','T3','T4','T5','T6','T7']; @endphp
                @forelse($schedules as $schedule)
                <tr class="hover:bg-gray-50">
                    <td class="table-td font-medium">{{ $schedule->room->name }}</td>
                    <td class="table-td">{{ $schedule->title ?? '—' }}</td>
                    <td class="table-td text-gray-500">{{ $schedule->recurring_type }}</td>
                    <td class="table-td">{{ isset($schedule->day_of_week) ? $days[$schedule->day_of_week] : '—' }}</td>
                    <td class="table-td">{{ $schedule->start_time }} – {{ $schedule->end_time }}</td>
                    <td class="table-td">
                        <a href="{{ route('admin.schedules.edit', $schedule) }}" class="text-blue-600 text-xs hover:underline mr-2">Sửa</a>
                        <button type="button" class="text-red-500 text-xs hover:underline"
                                @click="openConfirm('{{ route('admin.schedules.destroy', $schedule) }}')">Xoá</button>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-8 text-gray-400">Chưa có lịch cố định nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Confirm delete modal --}}
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
        <div class="bg-white rounded-xl shadow-xl pointer-events-auto" style="width:360px;max-width:calc(100vw - 2rem)" @click.stop>
            <div class="flex items-center gap-2.5 px-4 pt-4 pb-3">
                <div class="w-7 h-7 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-900 text-sm">Xoá lịch cố định</p>
                    <p class="text-xs text-gray-400 mt-0.5">Hành động này không thể hoàn tác.</p>
                </div>
                <button @click="confirmOpen = false" class="text-gray-300 hover:text-gray-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <p class="text-xs text-gray-500 px-4 pb-4">Bạn có chắc muốn xoá lịch cố định này không?</p>
            <div class="flex gap-2 justify-end px-4 pb-4">
                <button type="button" @click="confirmOpen = false" class="btn-secondary">Huỷ</button>
                <form :action="confirmUrl" method="POST" @submit="confirmOpen = false">
                    @csrf
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn-danger">Xoá</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
