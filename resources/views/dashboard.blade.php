@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="space-y-6" x-data="{
    stats: {
        pending:  {{ $pendingCount }},
        approved: {{ $approvedCount }},
        rooms:    {{ $availableRooms }},
        notifs:   {{ $unreadNotifs }},
    },
    lastUpdated: '',
    async refresh() {
        try {
            const res = await fetch('{{ route('dashboard.stats') }}', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (res.ok) {
                this.stats = await res.json();
                this.lastUpdated = new Date().toLocaleTimeString('vi-VN', {hour:'2-digit',minute:'2-digit',second:'2-digit'});
            }
        } catch {}
    },
    init() {
        this.lastUpdated = new Date().toLocaleTimeString('vi-VN', {hour:'2-digit',minute:'2-digit',second:'2-digit'});
        setInterval(() => this.refresh(), 30000);
    }
}">
    {{-- Greeting --}}
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Xin chào, {{ auth()->user()->name }} 👋</h1>
            <p class="text-gray-500 text-sm mt-1">{{ now()->format('l, d/m/Y') }}</p>
        </div>
        <span class="flex items-center gap-1.5 text-xs text-green-600">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
            </span>
            Live · <span x-text="lastUpdated"></span>
        </span>
    </div>

    {{-- Stats cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-yellow-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-yellow-600" x-text="stats.pending">{{ $pendingCount }}</div>
                <div class="text-xs text-gray-500 mt-0.5">Booking chờ duyệt</div>
            </div>
        </div>
        <div class="card flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-green-600" x-text="stats.approved">{{ $approvedCount }}</div>
                <div class="text-xs text-gray-500 mt-0.5">Booking đã duyệt</div>
            </div>
        </div>
        <div class="card flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16M9 21h6M3 21h18"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-blue-600" x-text="stats.rooms">{{ $availableRooms }}</div>
                <div class="text-xs text-gray-500 mt-0.5">Phòng sẵn sàng</div>
            </div>
        </div>
        <div class="card flex items-center gap-4">
            <div class="w-11 h-11 rounded-xl bg-purple-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
            </div>
            <div>
                <div class="text-2xl font-bold text-purple-600" x-text="stats.notifs">{{ $unreadNotifs }}</div>
                <div class="text-xs text-gray-500 mt-0.5">Thông báo chưa đọc</div>
            </div>
        </div>
    </div>

    {{-- Quick actions --}}
    <div class="card">
        <h2 class="font-semibold text-gray-700 mb-3">Thao tác nhanh</h2>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('bookings.create') }}" class="btn-primary">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Đặt lịch mới
            </a>
            <a href="{{ route('rooms.index') }}" class="btn-secondary">Xem phòng trống</a>
            <a href="{{ route('equipment.index') }}" class="btn-secondary">Xem thiết bị</a>
        </div>
    </div>

    {{-- Personal timetable --}}
    <div class="card" x-data x-init="$nextTick(() => window.initTimetable('dashboard-timetable', '{{ route('api.calendar.my-events') }}'))">
        <h2 class="font-semibold text-gray-700 mb-4">Lịch của tôi</h2>
        <div id="dashboard-timetable"></div>
    </div>
</div>
@endsection
