@extends('layouts.app')
@section('title', $room->name)

@section('content')
<div class="space-y-6 max-w-4xl mx-auto">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $room->name }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $room->building }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('rooms.schedule', $room) }}" class="btn-secondary">Xem lịch</a>
            @if($room->status === 'available')
                <a href="{{ route('bookings.create', ['type' => 'App\Models\Room', 'id' => $room->id]) }}" class="btn-primary">Đặt phòng</a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Info card --}}
        <div class="md:col-span-1 space-y-4">
            <div class="card">
                <h2 class="font-semibold text-gray-700 mb-3">Thông tin phòng</h2>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Trạng thái</dt>
                        <dd><x-badge :status="$room->status" /></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Loại phòng</dt>
                        <dd><x-badge :status="$room->type" /></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Sức chứa</dt>
                        <dd class="font-medium">{{ $room->capacity }} chỗ</dd>
                    </div>
                    @if($room->description)
                    <div class="pt-2 border-t">
                        <dt class="text-gray-500 mb-1">Mô tả</dt>
                        <dd class="text-gray-700">{{ $room->description }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            {{-- Equipment in room --}}
            @if($room->equipment->isNotEmpty())
            <div class="card">
                <h2 class="font-semibold text-gray-700 mb-3">Thiết bị trong phòng</h2>
                <ul class="space-y-2">
                    @foreach($room->equipment as $eq)
                    <li class="flex items-center justify-between text-sm">
                        <a href="{{ route('equipment.show', $eq) }}" class="text-blue-600 hover:underline">{{ $eq->name }}</a>
                        <span class="text-gray-500">×{{ $eq->quantity }}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif
        </div>

        {{-- Upcoming bookings --}}
        <div class="md:col-span-2">
            <div class="card">
                <h2 class="font-semibold text-gray-700 mb-3">Lịch sắp tới</h2>
                @forelse($upcomingBookings as $booking)
                <div class="flex items-center justify-between py-3 border-b last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $booking->title }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $booking->start_time->format('d/m/Y H:i') }} – {{ $booking->end_time->format('H:i') }}
                        </p>
                    </div>
                    <x-badge :status="$booking->status" />
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-4">Không có lịch đặt sắp tới.</p>
                @endforelse

                @if($upcomingBookings->isNotEmpty())
                <div class="mt-3 text-center">
                    <a href="{{ route('rooms.schedule', $room) }}" class="text-sm text-blue-600 hover:underline">Xem toàn bộ lịch →</a>
                </div>
                @endif
            </div>

            {{-- Fixed schedules --}}
            @if($room->schedules->isNotEmpty())
            <div class="card mt-4">
                <h2 class="font-semibold text-gray-700 mb-3">Lịch cố định</h2>
                @php $days = ['CN','T2','T3','T4','T5','T6','T7']; @endphp
                <div class="space-y-2">
                    @foreach($room->schedules as $schedule)
                    <div class="flex items-center justify-between text-sm">
                        <div>
                            <span class="font-medium">{{ $schedule->title ?? '(Không tên)' }}</span>
                            <span class="text-gray-400 ml-2">
                                {{ $schedule->recurring_type === 'weekly'
                                    ? ($days[$schedule->day_of_week] ?? '?')
                                    : ($schedule->recurring_type === 'daily' ? 'Hàng ngày' : 'Một lần') }}
                            </span>
                        </div>
                        <span class="text-gray-500">{{ $schedule->start_time }} – {{ $schedule->end_time }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

    </div>

    <div class="text-sm">
        <a href="{{ route('rooms.index') }}" class="text-gray-500 hover:text-gray-700">← Danh sách phòng</a>
    </div>
</div>
@endsection
