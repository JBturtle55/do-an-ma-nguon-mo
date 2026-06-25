@extends('layouts.app')
@section('title', 'Chi tiết Booking — Admin')

@section('content')
<div class="max-w-2xl mx-auto space-y-4" x-data="{ rejectOpen: false }"
     @keydown.escape.window="rejectOpen = false">
    <div class="flex items-center gap-3">
        <a href="{{ route('admin.bookings.index') }}" class="text-gray-400 hover:text-gray-600">← Danh sách</a>
        <h1 class="text-xl font-bold text-gray-800">Chi tiết Booking</h1>
    </div>

    <div class="card space-y-4">
        <div class="flex items-start justify-between">
            <h2 class="text-lg font-semibold">{{ $booking->title }}</h2>
            <x-badge :status="$booking->status" />
        </div>
        <hr class="border-gray-100">
        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div><dt class="text-gray-500">Người đặt</dt><dd class="font-medium mt-1">{{ $booking->user->name }}</dd></div>
            <div><dt class="text-gray-500">Đối tượng</dt><dd class="font-medium mt-1">{{ $booking->bookable?->name ?? '—' }}</dd></div>
            <div><dt class="text-gray-500">Bắt đầu</dt><dd class="font-medium mt-1">{{ $booking->start_time->format('d/m/Y H:i') }}</dd></div>
            <div><dt class="text-gray-500">Kết thúc</dt><dd class="font-medium mt-1">{{ $booking->end_time->format('d/m/Y H:i') }}</dd></div>
            @if($booking->purpose)
            <div class="col-span-2"><dt class="text-gray-500">Mục đích</dt><dd class="mt-1">{{ $booking->purpose }}</dd></div>
            @endif
            @if($booking->notes)
            <div class="col-span-2"><dt class="text-gray-500">Ghi chú/Lý do</dt><dd class="mt-1">{{ $booking->notes }}</dd></div>
            @endif
        </dl>

        @if($booking->status === 'pending')
        <div class="flex gap-3 pt-2 border-t border-gray-100">
            <form method="POST" action="{{ route('admin.bookings.approve', $booking) }}">
                @csrf @method('PATCH')
                <div class="flex gap-2 items-end">
                    <div>
                        <label class="form-label">Ghi chú duyệt (tuỳ chọn)</label>
                        <input type="text" name="notes" class="form-input w-64" placeholder="Ghi chú...">
                    </div>
                    <button class="btn-success">Duyệt booking</button>
                </div>
            </form>
            <button @click="rejectOpen = true" class="btn-danger self-end">Từ chối</button>
        </div>

        @endif
    </div>
</div>

{{-- Reject modal --}}
<div x-show="rejectOpen" @click="rejectOpen = false"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 bg-black/40 z-50" style="display:none"></div>
<div x-show="rejectOpen"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 scale-95 translate-y-2"
     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
     x-transition:leave-end="opacity-0 scale-95 translate-y-2"
     class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none" style="display:none">
    <div class="bg-white rounded-xl shadow-xl pointer-events-auto" style="width:400px;max-width:calc(100vw - 2rem)" @click.stop>
        <div class="flex items-center gap-2.5 px-4 pt-4 pb-3">
            <div class="w-7 h-7 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <div class="flex-1">
                <p class="font-semibold text-gray-900 text-sm">Từ chối Booking</p>
                <p class="text-xs text-gray-400 mt-0.5">Hành động này không thể hoàn tác.</p>
            </div>
            <button @click="rejectOpen = false" class="text-gray-300 hover:text-gray-500 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.bookings.reject', $booking) }}">
            @csrf @method('PATCH')
            <div class="px-4 pb-3">
                <label class="form-label">Lý do từ chối <span class="text-red-500">*</span></label>
                <textarea name="notes" rows="3" class="form-input mt-1" required placeholder="Nhập lý do từ chối..."></textarea>
            </div>
            <div class="flex gap-2 justify-end px-4 pb-4">
                <button type="button" @click="rejectOpen = false" class="btn-secondary">Huỷ</button>
                <button type="submit" class="btn-danger">Xác nhận từ chối</button>
            </div>
        </form>
    </div>
</div>
@endsection
