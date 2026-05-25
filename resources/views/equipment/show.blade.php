@extends('layouts.app')
@section('title', $equipment->name)

@section('content')
<div class="space-y-6 max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $equipment->name }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $equipment->category->name }}</p>
        </div>
        @if($equipment->status === 'available')
            <a href="{{ route('bookings.create', ['type' => 'App\Models\Equipment', 'id' => $equipment->id]) }}"
               class="btn-primary flex-shrink-0">Đặt thiết bị</a>
        @endif
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Info card --}}
        <div class="md:col-span-1">
            <div class="card">
                <h2 class="font-semibold text-gray-700 mb-3">Thông tin thiết bị</h2>
                <dl class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Trạng thái</dt>
                        <dd><x-badge :status="$equipment->status" /></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Số lượng</dt>
                        <dd class="font-medium">{{ $equipment->quantity }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Danh mục</dt>
                        <dd class="font-medium">{{ $equipment->category->name }}</dd>
                    </div>
                    @if($equipment->room)
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Vị trí</dt>
                        <dd>
                            <a href="{{ route('rooms.show', $equipment->room) }}" class="text-blue-600 hover:underline">
                                {{ $equipment->room->name }}
                            </a>
                        </dd>
                    </div>
                    @endif
                    @if($equipment->description)
                    <div class="pt-2 border-t">
                        <dt class="text-gray-500 mb-1">Mô tả</dt>
                        <dd class="text-gray-700">{{ $equipment->description }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>

        {{-- Recent bookings --}}
        <div class="md:col-span-2">
            <div class="card">
                <h2 class="font-semibold text-gray-700 mb-3">Lịch đặt gần đây</h2>
                @forelse($recentBookings as $booking)
                <div class="flex items-center justify-between py-3 border-b last:border-0">
                    <div>
                        <p class="text-sm font-medium text-gray-800">{{ $booking->title }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $booking->user->name }} &middot;
                            {{ $booking->start_time->format('d/m/Y H:i') }} – {{ $booking->end_time->format('H:i') }}
                        </p>
                    </div>
                    <x-badge :status="$booking->status" />
                </div>
                @empty
                <p class="text-sm text-gray-400 text-center py-4">Chưa có lịch đặt nào trong 7 ngày qua.</p>
                @endforelse
            </div>
        </div>

    </div>

    <div class="text-sm">
        <a href="{{ route('equipment.index') }}" class="text-gray-500 hover:text-gray-700">← Danh sách thiết bị</a>
    </div>
</div>
@endsection
