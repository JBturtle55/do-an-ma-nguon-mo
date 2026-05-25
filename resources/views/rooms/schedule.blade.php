@extends('layouts.app')
@section('title', $room->name . ' — Lịch')

@section('content')
<div class="space-y-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('rooms.index') }}" class="text-gray-400 hover:text-gray-600">← Phòng</a>
        <h1 class="text-xl font-bold text-gray-800">{{ $room->name }}</h1>
        <x-badge :status="$room->status" />
    </div>

    <div class="card">
        <div id="room-calendar" style="min-height: 500px;"></div>
    </div>

    @if($room->schedules->isNotEmpty())
    <div class="card">
        <h2 class="font-semibold text-gray-700 mb-3">Lịch cố định</h2>
        <div class="space-y-2">
            @foreach($room->schedules as $schedule)
                @php
                    $days = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
                @endphp
                <div class="flex items-center gap-3 text-sm text-gray-700">
                    <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-medium">
                        {{ $schedule->recurring_type === 'daily' ? 'Hàng ngày' : ($days[$schedule->day_of_week] ?? '') }}
                    </span>
                    <span>{{ $schedule->title ?? 'Lịch cố định' }}</span>
                    <span class="text-gray-400">{{ $schedule->start_time }} – {{ $schedule->end_time }}</span>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="flex gap-3">
        <a href="{{ route('bookings.create', ['type' => 'App\Models\Room', 'id' => $room->id]) }}" class="btn-primary">
            Đặt phòng này
        </a>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    if (window.initCalendar) {
        window.initCalendar(
            'room-calendar',
            '{{ route('api.calendar.room-events', $room->id) }}',
            { initialView: 'timeGridWeek' }
        );
    }
});
</script>
@endpush
