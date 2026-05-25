@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-gray-800">Admin Dashboard</h1>

    {{-- Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        @foreach([
            ['Tổng phòng', $stats['total_rooms'], 'text-blue-600'],
            ['Phòng trống', $stats['available_rooms'], 'text-green-600'],
            ['Thiết bị', $stats['total_equipment'], 'text-purple-600'],
            ['Người dùng', $stats['total_users'], 'text-indigo-600'],
            ['Booking chờ', $stats['pending_bookings'], 'text-yellow-600'],
            ['Duyệt hôm nay', $stats['today_bookings'], 'text-teal-600'],
        ] as [$label, $value, $color])
        <div class="card text-center py-4">
            <div class="text-2xl font-bold {{ $color }}">{{ $value }}</div>
            <div class="text-xs text-gray-500 mt-1">{{ $label }}</div>
        </div>
        @endforeach
    </div>

    {{-- Pending bookings --}}
    <div class="card">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-semibold text-gray-700">Booking chờ duyệt</h2>
            <a href="{{ route('admin.bookings.index', ['status' => 'pending']) }}" class="text-sm text-blue-600 hover:underline">
                Xem tất cả →
            </a>
        </div>
        @if($pendingBookings->isEmpty())
            <p class="text-gray-400 text-sm text-center py-4">Không có booking nào đang chờ duyệt.</p>
        @else
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
                        @foreach($pendingBookings as $booking)
                        <tr class="hover:bg-gray-50">
                            <td class="table-td font-medium">{{ $booking->title }}</td>
                            <td class="table-td text-gray-500">{{ $booking->user->name }}</td>
                            <td class="table-td text-gray-500">{{ $booking->bookable?->name ?? '—' }}</td>
                            <td class="table-td text-gray-500">{{ $booking->start_time->format('d/m H:i') }}</td>
                            <td class="table-td">
                                <a href="{{ route('admin.bookings.show', $booking) }}" class="text-blue-600 text-xs hover:underline">Xem & Duyệt</a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Quick links --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('admin.rooms.create') }}" class="card text-center hover:shadow-md transition-shadow text-blue-600 font-medium text-sm">+ Thêm phòng</a>
        <a href="{{ route('admin.equipment.create') }}" class="card text-center hover:shadow-md transition-shadow text-purple-600 font-medium text-sm">+ Thêm thiết bị</a>
        <a href="{{ route('admin.schedules.create') }}" class="card text-center hover:shadow-md transition-shadow text-green-600 font-medium text-sm">+ Thêm lịch cố định</a>
        <a href="{{ route('admin.reports.index') }}" class="card text-center hover:shadow-md transition-shadow text-indigo-600 font-medium text-sm">Xem báo cáo</a>
    </div>

    {{-- Overview timetable --}}
    <div class="card" x-data x-init="$nextTick(() => window.initTimetable('admin-timetable', '{{ route('api.calendar.events') }}'))">
        <h2 class="font-semibold text-gray-700 mb-4">Lịch tổng quan</h2>
        <div id="admin-timetable"></div>
    </div>
</div>
@endsection
