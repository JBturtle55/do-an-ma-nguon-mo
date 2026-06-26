@extends('layouts.app')
@section('title', 'Admin Dashboard')

@php
    $pendingBookingsJson = $pendingBookings->map(fn($b) => [
        'title'    => $b->title,
        'user'     => $b->user->name,
        'bookable' => $b->bookable?->name ?? '—',
        'time'     => $b->start_time->format('d/m H:i'),
        'url'      => route('admin.bookings.show', $b),
    ]);
@endphp
@section('content')
<div class="space-y-6" x-data="{
    stats: @json($stats),
    bookings: @json($pendingBookingsJson),
    lastUpdated: '',
    async refresh() {
        try {
            const [sRes, pRes] = await Promise.all([
                fetch('{{ route('admin.dashboard.stats') }}', { headers: {'X-Requested-With': 'XMLHttpRequest'} }),
                fetch('{{ route('admin.dashboard.pending') }}', { headers: {'X-Requested-With': 'XMLHttpRequest'} }),
            ]);
            if (sRes.ok) this.stats    = await sRes.json();
            if (pRes.ok) this.bookings = await pRes.json();
            this.lastUpdated = new Date().toLocaleTimeString('vi-VN', {hour:'2-digit',minute:'2-digit',second:'2-digit'});
        } catch {}
    },
    init() {
        this.lastUpdated = new Date().toLocaleTimeString('vi-VN', {hour:'2-digit',minute:'2-digit',second:'2-digit'});
        setInterval(() => this.refresh(), 30000);
    }
}">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-800">Admin Dashboard</h1>
        <span class="flex items-center gap-1.5 text-xs text-green-600">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
            </span>
            Live · <span x-text="lastUpdated"></span>
        </span>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="card flex flex-col items-center gap-2 py-5">
            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16M9 21h6M3 21h18"/>
                </svg>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600" x-text="stats.total_rooms">{{ $stats['total_rooms'] }}</div>
                <div class="text-xs text-gray-500 mt-0.5">Tổng phòng</div>
            </div>
        </div>
        <div class="card flex flex-col items-center gap-2 py-5">
            <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600" x-text="stats.available_rooms">{{ $stats['available_rooms'] }}</div>
                <div class="text-xs text-gray-500 mt-0.5">Phòng trống</div>
            </div>
        </div>
        <div class="card flex flex-col items-center gap-2 py-5">
            <div class="w-10 h-10 rounded-xl bg-purple-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/>
                </svg>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-purple-600" x-text="stats.total_equipment">{{ $stats['total_equipment'] }}</div>
                <div class="text-xs text-gray-500 mt-0.5">Thiết bị</div>
            </div>
        </div>
        <div class="card flex flex-col items-center gap-2 py-5">
            <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-indigo-600" x-text="stats.total_users">{{ $stats['total_users'] }}</div>
                <div class="text-xs text-gray-500 mt-0.5">Người dùng</div>
            </div>
        </div>
        <div class="card flex flex-col items-center gap-2 py-5">
            <div class="w-10 h-10 rounded-xl bg-yellow-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-yellow-600" x-text="stats.pending_bookings">{{ $stats['pending_bookings'] }}</div>
                <div class="text-xs text-gray-500 mt-0.5">Booking chờ</div>
            </div>
        </div>
        <div class="card flex flex-col items-center gap-2 py-5">
            <div class="w-10 h-10 rounded-xl bg-teal-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-teal-600" x-text="stats.today_bookings">{{ $stats['today_bookings'] }}</div>
                <div class="text-xs text-gray-500 mt-0.5">Duyệt hôm nay</div>
            </div>
        </div>
    </div>

    {{-- Pending bookings --}}
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-700">
                Booking chờ duyệt
                <span class="ml-1.5 text-xs font-normal text-yellow-600 bg-yellow-50 px-2 py-0.5 rounded-full" x-text="bookings.length + ' booking'" x-show="bookings.length > 0"></span>
            </h2>
            <a href="{{ route('admin.bookings.index', ['status' => 'pending']) }}" class="text-sm text-blue-600 hover:underline">
                Xem tất cả →
            </a>
        </div>
        <template x-if="bookings.length === 0">
            <p class="text-gray-400 text-sm text-center py-4">Không có booking nào đang chờ duyệt.</p>
        </template>
        <template x-if="bookings.length > 0">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="table-th">Tiêu đề</th>
                            <th class="table-th">Người đặt</th>
                            <th class="table-th">Đối tượng</th>
                            <th class="table-th">Thời gian</th>
                            <th class="table-th">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <template x-for="b in bookings" :key="b.url">
                            <tr class="hover:bg-gray-50">
                                <td class="table-td font-medium" x-text="b.title"></td>
                                <td class="table-td text-gray-500" x-text="b.user"></td>
                                <td class="table-td text-gray-500" x-text="b.bookable"></td>
                                <td class="table-td text-gray-500" x-text="b.time"></td>
                                <td class="table-td">
                                    <a :href="b.url" class="text-blue-600 text-xs hover:underline">Xem & Duyệt</a>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </template>
    </div>

    {{-- Quick links --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('admin.rooms.create') }}" class="card flex items-center gap-3 hover:shadow-md transition-shadow group">
            <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center flex-shrink-0 group-hover:bg-blue-200 transition-colors">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <span class="text-sm font-medium text-blue-600">Thêm phòng</span>
        </a>
        <a href="{{ route('admin.equipment.create') }}" class="card flex items-center gap-3 hover:shadow-md transition-shadow group">
            <div class="w-9 h-9 rounded-lg bg-purple-100 flex items-center justify-center flex-shrink-0 group-hover:bg-purple-200 transition-colors">
                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
            </div>
            <span class="text-sm font-medium text-purple-600">Thêm thiết bị</span>
        </a>
        <a href="{{ route('admin.schedules.create') }}" class="card flex items-center gap-3 hover:shadow-md transition-shadow group">
            <div class="w-9 h-9 rounded-lg bg-green-100 flex items-center justify-center flex-shrink-0 group-hover:bg-green-200 transition-colors">
                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
            <span class="text-sm font-medium text-green-600">Thêm lịch cố định</span>
        </a>
        <a href="{{ route('admin.reports.index') }}" class="card flex items-center gap-3 hover:shadow-md transition-shadow group">
            <div class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center flex-shrink-0 group-hover:bg-indigo-200 transition-colors">
                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <span class="text-sm font-medium text-indigo-600">Xem báo cáo</span>
        </a>
    </div>

    {{-- Overview timetable --}}
    <div class="card" x-data x-init="$nextTick(() => window.initTimetable('admin-timetable', '{{ route('api.calendar.events') }}'))">
        <h2 class="font-semibold text-gray-700 mb-4">Lịch tổng quan</h2>
        <div id="admin-timetable"></div>
    </div>
</div>
@endsection
