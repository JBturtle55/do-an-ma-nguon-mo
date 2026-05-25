@extends('layouts.app')
@section('title', 'Sửa thiết bị')
@section('content')
<div class="max-w-lg mx-auto space-y-4">
    <h1 class="text-xl font-bold text-gray-800">Sửa thiết bị: {{ $equipment->name }}</h1>
    <div class="card">
        <form method="POST" action="{{ route('admin.equipment.update', $equipment) }}" class="space-y-4">
            @csrf @method('PUT')
            <div><label class="form-label">Tên thiết bị *</label><input type="text" name="name" value="{{ old('name', $equipment->name) }}" class="form-input" required></div>
            <div><label class="form-label">Danh mục *</label><select name="category_id" class="form-input" required>@foreach($categories as $cat)<option value="{{ $cat->id }}" @selected(old('category_id', $equipment->category_id) == $cat->id)>{{ $cat->name }}</option>@endforeach</select></div>
            <div><label class="form-label">Phòng chứa</label><select name="room_id" class="form-input"><option value="">-- Không có --</option>@foreach($rooms as $room)<option value="{{ $room->id }}" @selected(old('room_id', $equipment->room_id) == $room->id)>{{ $room->name }}</option>@endforeach</select></div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="form-label">Số lượng *</label><input type="number" name="quantity" value="{{ old('quantity', $equipment->quantity) }}" min="1" class="form-input" required></div>
                <div><label class="form-label">Trạng thái</label><select name="status" class="form-input"><option value="available" @selected($equipment->status === 'available')>Sẵn sàng</option><option value="maintenance" @selected($equipment->status === 'maintenance')>Bảo trì</option><option value="unavailable" @selected($equipment->status === 'unavailable')>Không dùng</option></select></div>
            </div>
            <div><label class="form-label">Mô tả</label><textarea name="description" rows="3" class="form-input">{{ old('description', $equipment->description) }}</textarea></div>
            <div class="flex gap-3"><button type="submit" class="btn-primary">Cập nhật</button><a href="{{ route('admin.equipment.index') }}" class="btn-secondary">Huỷ</a></div>
        </form>
    </div>
</div>
@endsection
