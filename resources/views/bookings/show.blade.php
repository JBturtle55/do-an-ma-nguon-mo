@extends('layouts.app')
@section('title', 'Chi tiết Booking')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">
    <div class="flex items-center gap-3">
        <a href="{{ route('bookings.index') }}" class="text-gray-400 hover:text-gray-600">← Quay lại</a>
        <h1 class="text-xl font-bold text-gray-800">Chi tiết Booking</h1>
    </div>

    <div class="card space-y-4">
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-800">{{ $booking->title }}</h2>
                <p class="text-sm text-gray-500 mt-1">Tạo bởi {{ $booking->user->name }}</p>
            </div>
            <x-badge :status="$booking->status" />
        </div>

        <hr class="border-gray-100">

        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500">Đối tượng đặt</dt>
                <dd class="font-medium text-gray-800 mt-1">{{ $booking->bookable?->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Loại</dt>
                <dd class="font-medium mt-1">
                    {{ class_basename($booking->bookable_type) === 'Room' ? 'Phòng' : 'Thiết bị' }}
                </dd>
            </div>
            <div>
                <dt class="text-gray-500">Bắt đầu</dt>
                <dd class="font-medium mt-1">{{ $booking->start_time->format('d/m/Y H:i') }}</dd>
            </div>
            <div>
                <dt class="text-gray-500">Kết thúc</dt>
                <dd class="font-medium mt-1">{{ $booking->end_time->format('d/m/Y H:i') }}</dd>
            </div>
            @if($booking->purpose)
            <div class="col-span-2">
                <dt class="text-gray-500">Mục đích</dt>
                <dd class="mt-1 text-gray-800">{{ $booking->purpose }}</dd>
            </div>
            @endif
            @if($booking->notes)
            <div class="col-span-2">
                <dt class="text-gray-500">Ghi chú</dt>
                <dd class="mt-1 text-gray-800">{{ $booking->notes }}</dd>
            </div>
            @endif
            @if($booking->approver)
            <div class="col-span-2">
                <dt class="text-gray-500">Được duyệt/từ chối bởi</dt>
                <dd class="font-medium mt-1">{{ $booking->approver->name }}</dd>
            </div>
            @endif
        </dl>

        @if($booking->equipment->isNotEmpty())
        <div>
            <h3 class="text-sm font-semibold text-gray-600 mb-2">Thiết bị kèm theo</h3>
            <div class="space-y-1">
                @foreach($booking->equipment as $equip)
                    <div class="flex justify-between text-sm text-gray-700">
                        <span>{{ $equip->name }}</span>
                        <span class="text-gray-500">x{{ $equip->pivot->quantity }}</span>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        <div class="flex gap-3 pt-2">
            @if(in_array($booking->status, ['pending', 'approved']))
                <form method="POST" action="{{ route('bookings.cancel', $booking) }}"
                      onsubmit="return confirm('Huỷ booking này?')">
                    @csrf @method('PATCH')
                    <button class="btn-danger">Huỷ booking</button>
                </form>
            @endif
            <a href="{{ route('bookings.index') }}" class="btn-secondary">Quay lại</a>
        </div>
    </div>
</div>
@endsection
