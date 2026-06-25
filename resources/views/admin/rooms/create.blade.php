@extends('layouts.app')
@section('title', 'Thêm phòng mới')
@section('content')
<div class="max-w-lg mx-auto space-y-4">
    <h1 class="text-xl font-bold text-gray-800">Thêm phòng mới</h1>
    <div class="card">
        <form method="POST" action="{{ route('admin.rooms.store') }}" class="space-y-4" enctype="multipart/form-data">
            @csrf
            <div>
                <label class="form-label">Tên phòng *</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-input" required>
                @error('name')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Toà nhà</label>
                <input type="text" name="building" value="{{ old('building') }}" class="form-input">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="form-label">Sức chứa *</label>
                    <input type="number" name="capacity" value="{{ old('capacity', 20) }}" min="1" class="form-input" required>
                </div>
                <div>
                    <label class="form-label">Loại phòng *</label>
                    <select name="type" class="form-input" required>
                        <option value="lab" @selected(old('type') === 'lab')>Lab</option>
                        <option value="classroom" @selected(old('type') === 'classroom')>Phòng học</option>
                        <option value="workshop" @selected(old('type') === 'workshop')>Xưởng</option>
                    </select>
                </div>
            </div>
            <div>
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-input">
                    <option value="available" @selected(old('status') === 'available')>Sẵn sàng</option>
                    <option value="maintenance" @selected(old('status') === 'maintenance')>Bảo trì</option>
                    <option value="unavailable" @selected(old('status') === 'unavailable')>Không dùng</option>
                </select>
            </div>
            <div>
                <label class="form-label">Mô tả</label>
                <textarea name="description" rows="3" class="form-input">{{ old('description') }}</textarea>
            </div>
            <div>
                <label class="form-label">Ảnh phòng</label>
                <input type="file" name="image" accept="image/*" class="form-input">
                @error('image')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn-primary">Lưu phòng</button>
                <a href="{{ route('admin.rooms.index') }}" class="btn-secondary">Huỷ</a>
            </div>
        </form>
    </div>
</div>
@endsection
