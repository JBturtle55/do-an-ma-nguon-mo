@extends('layouts.app')
@section('title', 'Thiết bị')

@section('content')
<div class="space-y-4">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-800">Thiết bị thực hành</h1>
    </div>

    <div class="card">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên thiết bị..." class="form-input w-48">
            <select name="category" class="form-input w-48">
                <option value="">Tất cả danh mục</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" @selected(request('category') == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="status" class="form-input w-40">
                <option value="">Tất cả trạng thái</option>
                <option value="available" @selected(request('status') === 'available')>Sẵn sàng</option>
                <option value="maintenance" @selected(request('status') === 'maintenance')>Bảo trì</option>
            </select>
            <button type="submit" class="btn-secondary">Lọc</button>
            @if(request()->hasAny(['search','category','status']))
                <a href="{{ route('equipment.index') }}" class="btn-secondary">Xoá bộ lọc</a>
            @endif
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @forelse($equipment as $equip)
            <div class="card hover:shadow-md transition-shadow">
                <div class="w-full h-16 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg mb-3 flex items-center justify-center">
                    <svg class="w-8 h-8 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 3H5a2 2 0 00-2 2v4m6-6h10a2 2 0 012 2v4M9 3v18m0 0h10a2 2 0 002-2v-4M9 21H5a2 2 0 01-2-2v-4m0 0h18"/>
                    </svg>
                </div>
                <a href="{{ route('equipment.show', $equip) }}" class="font-semibold text-gray-800 text-sm mb-1 hover:text-blue-600 hover:underline block">{{ $equip->name }}</a>
                <div class="space-y-1 text-xs text-gray-500 mb-3">
                    <div>{{ $equip->category->name }}</div>
                    <div>Số lượng: {{ $equip->quantity }}</div>
                    @if($equip->room)<div>Vị trí: {{ $equip->room->name }}</div>@endif
                    <x-badge :status="$equip->status" />
                </div>
                @if($equip->status === 'available')
                    <a href="{{ route('bookings.create', ['type' => 'App\Models\Equipment', 'id' => $equip->id]) }}"
                       class="btn-primary text-xs w-full justify-center">Đặt thiết bị</a>
                @endif
            </div>
        @empty
            <div class="col-span-4 text-center py-12 text-gray-400">Không tìm thấy thiết bị nào.</div>
        @endforelse
    </div>

    @if($equipment->hasPages())
        <div>{{ $equipment->links() }}</div>
    @endif
</div>
@endsection
