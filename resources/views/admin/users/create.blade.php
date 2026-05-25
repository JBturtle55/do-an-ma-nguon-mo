@extends('layouts.app')
@section('title', 'Thêm người dùng')
@section('content')
<div class="max-w-lg mx-auto space-y-4">
    <h1 class="text-xl font-bold text-gray-800">Thêm người dùng mới</h1>
    <div class="card">
        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="form-label">Họ tên *</label>
                <input type="text" name="name" value="{{ old('name') }}" class="form-input" required>
                @error('name')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-input" required>
                @error('email')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Mật khẩu *</label>
                <input type="password" name="password" class="form-input" required>
                @error('password')<p class="form-error">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="form-label">Xác nhận mật khẩu *</label>
                <input type="password" name="password_confirmation" class="form-input" required>
            </div>
            <div>
                <label class="form-label">Vai trò *</label>
                <select name="role" class="form-input" required>
                    <option value="student" @selected(old('role') === 'student')>Sinh viên</option>
                    <option value="lecturer" @selected(old('role') === 'lecturer')>Giảng viên</option>
                    <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn-primary">Tạo tài khoản</button>
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">Huỷ</a>
            </div>
        </form>
    </div>
</div>
@endsection
