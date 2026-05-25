@extends('layouts.app')
@section('title', 'Chi tiết Booking — Admin')

@section('content')
<div class="max-w-2xl mx-auto space-y-4" x-data="{ rejectOpen: false }">
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

        <div x-show="rejectOpen" class="border-t border-gray-100 pt-4" style="display:none">
            <form method="POST" action="{{ route('admin.bookings.reject', $booking) }}">
                @csrf @method('PATCH')
                <div class="mb-3">
                    <label class="form-label">Lý do từ chối <span class="text-red-500">*</span></label>
                    <textarea name="notes" rows="3" class="form-input" required placeholder="Nhập lý do từ chối..."></textarea>
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="btn-danger">Xác nhận từ chối</button>
                    <button type="button" @click="rejectOpen = false" class="btn-secondary">Huỷ</button>
                </div>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
