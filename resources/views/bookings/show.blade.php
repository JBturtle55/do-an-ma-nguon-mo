@extends('layouts.app')
@section('title', 'Chi tiết Booking')

@section('content')
<div class="max-w-2xl mx-auto space-y-4" x-data="{
    confirmOpen: false,
    openConfirm() { this.confirmOpen = true; }
}" @keydown.escape.window="confirmOpen = false">
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
                <button type="button" class="btn-danger" @click="openConfirm()">Huỷ booking</button>
            @endif
            <a href="{{ route('bookings.index') }}" class="btn-secondary">Quay lại</a>
        </div>
    </div>
</div>

{{-- Confirm cancel modal --}}
<div x-show="confirmOpen" @click="confirmOpen = false"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black/40 z-50" style="display:none"></div>
<div x-show="confirmOpen"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 scale-95 translate-y-2"
     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
     x-transition:leave-end="opacity-0 scale-95 translate-y-2"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none" style="display:none">
    <div class="bg-white rounded-xl shadow-xl pointer-events-auto" style="width:360px;max-width:calc(100vw - 2rem)" @click.stop>
        <div class="flex items-center gap-2.5 px-4 pt-4 pb-3">
            <div class="w-7 h-7 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <div class="flex-1">
                <p class="font-semibold text-gray-900 text-sm">Huỷ booking</p>
                <p class="text-xs text-gray-400 mt-0.5">Hành động này không thể hoàn tác.</p>
            </div>
            <button @click="confirmOpen = false" class="text-gray-300 hover:text-gray-500 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <p class="text-xs text-gray-500 px-4 pb-4">Bạn có chắc muốn huỷ booking này không?</p>
        <div class="flex gap-2 justify-end px-4 pb-4">
            <button type="button" @click="confirmOpen = false" class="btn-secondary">Không</button>
            <form method="POST" action="{{ route('bookings.cancel', $booking) }}" @submit="confirmOpen = false">
                @csrf @method('PATCH')
                <button type="submit" class="btn-danger">Huỷ booking</button>
            </form>
        </div>
    </div>
</div>
@endsection
