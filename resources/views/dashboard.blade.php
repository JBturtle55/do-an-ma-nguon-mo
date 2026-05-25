@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="space-y-6">
    {{-- Greeting --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Xin chào, {{ auth()->user()->name }} 👋</h1>
        <p class="text-gray-500 text-sm mt-1">{{ now()->format('l, d/m/Y') }}</p>
    </div>

    {{-- Stats cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card text-center">
            <div class="text-3xl font-bold text-yellow-600">{{ $pendingCount }}</div>
            <div class="text-sm text-gray-500 mt-1">Booking chờ duyệt</div>
        </div>
        <div class="card text-center">
            <div class="text-3xl font-bold text-green-600">{{ $approvedCount }}</div>
            <div class="text-sm text-gray-500 mt-1">Booking đã duyệt</div>
        </div>
        <div class="card text-center">
            <div class="text-3xl font-bold text-blue-600">{{ $availableRooms }}</div>
            <div class="text-sm text-gray-500 mt-1">Phòng sẵn sàng</div>
        </div>
        <div class="card text-center">
            <div class="text-3xl font-bold text-purple-600">{{ $unreadNotifs }}</div>
            <div class="text-sm text-gray-500 mt-1">Thông báo chưa đọc</div>
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
