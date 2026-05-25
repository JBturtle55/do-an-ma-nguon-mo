@extends('layouts.app')
@section('title', 'Thêm thiết bị')
@section('content')
<div class="max-w-lg mx-auto space-y-4">
    <h1 class="text-xl font-bold text-gray-800">Thêm thiết bị mới</h1>
    <div class="card">
        <form method="POST" action="{{ route('admin.equipment.store') }}" class="space-y-4">
            @csrf
            <div><label class="form-label">Tên thiết bị *</label><input type="text" name="name" value="{{ old('name') }}" class="form-input" required>@error('name')<p class="form-error">{{ $message }}</p>@enderror</div>
            <div><label class="form-label">Danh mục *</label><select name="category_id" class="form-input" required>@foreach($categories as $cat)<option value="{{ $cat->id }}" @selected(old('category_id') == $cat->id)>{{ $cat->name }}</option>@endforeach</select></div>
            <div><label class="form-label">Phòng chứa</label><select name="room_id" class="form-input"><option value="">-- Không có --</option>@foreach($rooms as $room)<option value="{{ $room->id }}" @selected(old('room_id') == $room->id)>{{ $room->name }}</option>@endforeach</select></div>
            <div class="grid grid-cols-2 gap-4"><div><label class="form-label">Số lượng *</label><input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" class="form-input" required></div><div><label class="form-label">Trạng thái</label><select name="status" class="form-input"><option value="available">Sẵn sàng</option><option value="maintenance">Bảo trì</option><option value="unavailable">Không dùng</option></select></div></div>
            <div><label class="form-label">Mô tả</label><textarea name="description" rows="3" class="form-input">{{ old('description') }}</textarea></div>
            <div class="flex gap-3"><button type="submit" class="btn-primary">Lưu</button><a href="{{ route('admin.equipment.index') }}" class="btn-secondary">Huỷ</a></div>
        </form>
    </div>
</div>
@endsection
