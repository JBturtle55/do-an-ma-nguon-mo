@extends('layouts.app')
@section('title', 'Lịch đặt của tôi')

@section('content')
<div class="space-y-4" x-data="{
    view: 'list',
    timetableReady: false,
    switchView(v) {
        this.view = v;
        if (v === 'calendar' && !this.timetableReady) {
            this.timetableReady = true;
            this.$nextTick(() => window.initTimetable('my-calendar', '/api/calendar/my-events'));
        }
    },
    cancelOpen: false,
    cancelUrl: '',
    openCancel(url) { this.cancelUrl = url; this.cancelOpen = true; }
}" @keydown.escape.window="cancelOpen = false">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
        <h1 class="text-xl font-bold text-gray-800">Lịch đặt của tôi</h1>
        <div class="flex items-center gap-3">
            {{-- Toggle list / calendar --}}
            <div class="flex rounded-lg border border-gray-200 overflow-hidden text-sm">
                <button @click="switchView('list')"
                        :class="view === 'list' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                        class="px-3 py-1.5 flex items-center gap-1.5 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                    </svg>
                    Danh sách
                </button>
                <button @click="switchView('calendar')"
                        :class="view === 'calendar' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                        class="px-3 py-1.5 flex items-center gap-1.5 border-l border-gray-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Lịch
                </button>
            </div>
            <a href="{{ route('bookings.create') }}" class="btn-primary">+ Đặt lịch mới</a>
        </div>
    </div>

    {{-- LIST VIEW --}}
    <div x-show="view === 'list'">
        {{-- Filters --}}
        <div class="card mb-4">
            <form method="GET" class="flex flex-wrap gap-3">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm..." class="form-input w-48">
                <select name="status" class="form-input w-40">
                    <option value="">Tất cả trạng thái</option>
                    @foreach(['pending' => 'Chờ duyệt', 'approved' => 'Đã duyệt', 'rejected' => 'Từ chối', 'cancelled' => 'Đã huỷ'] as $val => $label)
                        <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
                    @endforeach
                </select>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input w-40">
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input w-40">
                <button type="submit" class="btn-secondary">Lọc</button>
                @if(request()->hasAny(['search','status','date_from','date_to']))
                    <a href="{{ route('bookings.index') }}" class="btn-secondary">Xoá bộ lọc</a>
                @endif
            </form>
        </div>

        <div class="card p-0 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="table-th">Tiêu đề</th>
                        <th class="table-th">Đối tượng</th>
                        <th class="table-th">Thời gian</th>
                        <th class="table-th">Trạng thái</th>
                        <th class="table-th">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($bookings as $booking)
                        <tr class="hover:bg-gray-50">
                            <td class="table-td font-medium">
                                <a href="{{ route('bookings.show', $booking) }}" class="text-blue-600 hover:underline">
                                    {{ $booking->title }}
                                </a>
                            </td>
                            <td class="table-td text-gray-500">{{ $booking->bookable?->name ?? '—' }}</td>
                            <td class="table-td text-gray-500 whitespace-nowrap">
                                {{ $booking->start_time->format('d/m/Y H:i') }} – {{ $booking->end_time->format('H:i') }}
                            </td>
                            <td class="table-td"><x-badge :status="$booking->status"/></td>
                            <td class="table-td">
                                <a href="{{ route('bookings.show', $booking) }}" class="text-blue-600 text-xs hover:underline">Chi tiết</a>
                                @if(in_array($booking->status, ['pending', 'approved']))
                                    <button type="button" class="text-red-500 text-xs hover:underline ml-2"
                                            @click="openCancel('{{ route('bookings.cancel', $booking) }}')">Huỷ</button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-8 text-gray-400">Chưa có booking nào.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if($bookings->hasPages())
                <div class="px-4 py-3 border-t border-gray-100">{{ $bookings->links() }}</div>
            @endif
        </div>
    </div>

    {{-- CALENDAR VIEW --}}
    <div x-show="view === 'calendar'">
        <div class="card">
            {{-- Legend --}}
            <div class="flex flex-wrap gap-4 mb-4 pb-3 border-b border-gray-100 text-xs text-gray-500">
                <span class="flex items-center gap-1.5">
                    <span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#fffbeb;border-left:3px solid #f59e0b;"></span> Chờ duyệt
                </span>
                <span class="flex items-center gap-1.5">
                    <span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#f0fdf4;border-left:3px solid #16a34a;"></span> Đã duyệt
                </span>
                <span class="flex items-center gap-1.5">
                    <span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#fef2f2;border-left:3px solid #ef4444;"></span> Từ chối
                </span>
                <span class="flex items-center gap-1.5">
                    <span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#f9fafb;border-left:3px solid #9ca3af;"></span> Đã huỷ
                </span>
            </div>
            <div id="my-calendar"></div>
        </div>
    </div>

    {{-- Confirm cancel modal --}}
    <div x-show="cancelOpen" @click="cancelOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/40 z-50" style="display:none"></div>
    <div x-show="cancelOpen"
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-900 text-sm">Huỷ booking</p>
                    <p class="text-xs text-gray-400 mt-0.5">Hành động này không thể hoàn tác.</p>
                </div>
                <button @click="cancelOpen = false" class="text-gray-300 hover:text-gray-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <p class="text-xs text-gray-500 px-4 pb-4">Bạn có chắc muốn huỷ booking này không?</p>
            <div class="flex gap-2 justify-end px-4 pb-4">
                <button type="button" @click="cancelOpen = false" class="btn-secondary">Không</button>
                <form :action="cancelUrl" method="POST" @submit="cancelOpen = false">
                    @csrf
                    <input type="hidden" name="_method" value="PATCH">
                    <button type="submit" class="btn-danger">Huỷ booking</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
