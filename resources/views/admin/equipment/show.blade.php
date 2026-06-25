@extends('layouts.app')
@section('title', $equipment->name)
@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.equipment.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Danh sách thiết bị</a>
            <h1 class="text-2xl font-bold text-gray-800 mt-1">{{ $equipment->name }}</h1>
        </div>
        <a href="{{ route('admin.equipment.edit', $equipment) }}" class="btn-primary">Sửa thiết bị</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="card space-y-3">
            <h2 class="font-semibold text-gray-700">Thông tin thiết bị</h2>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between"><dt class="text-gray-500">Danh mục</dt><dd class="font-medium">{{ $equipment->category->name }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Số lượng</dt><dd class="font-medium">{{ $equipment->quantity }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Vị trí</dt><dd class="font-medium">{{ $equipment->room?->name ?? '—' }}</dd></div>
                <div class="flex justify-between"><dt class="text-gray-500">Trạng thái</dt><dd><x-badge :status="$equipment->status"/></dd></div>
            </dl>
            @if($equipment->description)
                <p class="text-sm text-gray-600 pt-2 border-t">{{ $equipment->description }}</p>
            @endif
        </div>

        <div class="md:col-span-2">
            <div class="card">
                <h2 class="font-semibold text-gray-700 mb-3">Booking gần đây</h2>
                @forelse($equipment->bookings as $booking)
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
        </div>
    </div>
</div>
@endsection
