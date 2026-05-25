@extends('layouts.app')
@section('title', 'Danh sách Phòng')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-800">Phòng Lab & Thực hành</h1>
        <a href="{{ route('bookings.create', ['type' => 'App\Models\Room']) }}" class="btn-primary">+ Đặt phòng</a>
    </div>

    <div class="card">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên phòng..." class="form-input w-48">
            <select name="type" class="form-input w-40">
                <option value="">Tất cả loại</option>
                <option value="lab" @selected(request('type') === 'lab')>Lab</option>
                <option value="classroom" @selected(request('type') === 'classroom')>Phòng học</option>
                <option value="workshop" @selected(request('type') === 'workshop')>Xưởng</option>
            </select>
            <select name="status" class="form-input w-40">
                <option value="">Tất cả trạng thái</option>
                <option value="available" @selected(request('status') === 'available')>Sẵn sàng</option>
                <option value="maintenance" @selected(request('status') === 'maintenance')>Bảo trì</option>
            </select>
            <button type="submit" class="btn-secondary">Lọc</button>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($rooms as $room)
            <div class="card hover:shadow-md transition-shadow">
                @if($room->image)
                    <img src="{{ asset('storage/' . $room->image) }}" alt="{{ $room->name }}"
                         class="w-full h-32 object-cover rounded-lg mb-3">
                @else
                    <div class="w-full h-24 bg-gradient-to-br from-blue-100 to-blue-200 rounded-lg mb-3 flex items-center justify-center">
                        <svg class="w-10 h-10 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16M9 21h6M3 21h18"/>
                        </svg>
                    </div>
                @endif

                <div class="flex items-start justify-between mb-2">
                    <h3 class="font-semibold text-gray-800 text-sm leading-tight">{{ $room->name }}</h3>
                    <x-badge :status="$room->status" class="ml-2 flex-shrink-0" />
                </div>

                <div class="space-y-1 text-xs text-gray-500 mb-3">
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                        {{ $room->building ?? 'N/A' }}
                    </div>
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        {{ $room->capacity }} chỗ ngồi
                    </div>
                    <x-badge :status="$room->type" />
                </div>

                <div class="flex gap-2">
                    <a href="{{ route('rooms.schedule', $room) }}" class="btn-secondary text-xs flex-1 justify-center">Xem lịch</a>
                    @if($room->status === 'available')
                        <a href="{{ route('bookings.create', ['type' => 'App\Models\Room', 'id' => $room->id]) }}"
                           class="btn-primary text-xs flex-1 justify-center">Đặt ngay</a>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-12 text-gray-400">Không tìm thấy phòng nào.</div>
        @endforelse
    </div>

    @if($rooms->hasPages())
        <div>{{ $rooms->links() }}</div>
    @endif
</div>
@endsection
