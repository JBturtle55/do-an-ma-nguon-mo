@extends('layouts.app')
@section('title', $room->name)
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.rooms.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Danh sách phòng</a>
            <h1 class="text-2xl font-bold text-gray-800 mt-1">{{ $room->name }}</h1>
        </div>
        <a href="{{ route('admin.rooms.edit', $room) }}" class="btn-primary">Sửa phòng</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card space-y-3">
            <h2 class="font-semibold text-gray-700">Thông tin phòng</h2>
            @if($room->image)
                <img src="{{ asset('storage/' . $room->image) }}" alt="{{ $room->name }}" class="w-full h-40 object-cover rounded-lg">
            @endif
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Toà nhà</dt><dd class="font-medium">{{ $room->building ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Sức chứa</dt><dd class="font-medium">{{ $room->capacity }} chỗ</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Loại</dt><dd><x-badge :status="$room->type"/></dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Trạng thái</dt><dd><x-badge :status="$room->status"/></dd></div>
            </dl>
            @if($room->description)
                <p class="text-sm text-gray-600 pt-2 border-t">{{ $room->description }}</p>
            @endif
        </div>

        <div class="md:col-span-2 space-y-6">
            <div class="card">
                <h2 class="font-semibold text-gray-700 mb-3">Booking gần đây</h2>
                @forelse($room->bookings as $booking)
                <div class="flex items-center justify-between py-2.5 border-b last:border-0">
                    <div>
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="text-sm font-medium text-blue-600 hover:underline">{{ $booking->title }}</a>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $booking->user->name }} · {{ $booking->start_time->format('d/m/Y H:i') }} – {{ $booking->end_time->format('H:i') }}</p>
                    </div>
                    <x-badge :status="$booking->status"/>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-4">Chưa có booking nào.</p>
                @endforelse
            </div>

            <div class="card">
                <h2 class="font-semibold text-gray-700 mb-3">Lịch sử bảo trì</h2>
                @forelse($room->maintenanceLogs as $log)
                <div class="flex items-center justify-between py-2.5 border-b last:border-0">
                    <p class="text-sm text-gray-700 truncate max-w-xs">{{ $log->description }}</p>
                    <div class="flex items-center gap-3 flex-shrink-0 ml-3">
                        <span class="text-xs text-gray-400">{{ $log->created_at->format('d/m/Y') }}</span>
                        <x-badge :status="$log->status"/>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-4">Không có sự cố nào.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
