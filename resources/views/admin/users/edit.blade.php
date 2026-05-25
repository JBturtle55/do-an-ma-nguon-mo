@extends('layouts.app')
@section('title', 'Sửa người dùng')
@section('content')
<div class="max-w-lg mx-auto space-y-4">
    <h1 class="text-xl font-bold text-gray-800">Sửa tài khoản: {{ $user->name }}</h1>
    <div class="card">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="form-label">Họ tên *</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input" required>
            </div>
            <div>
                <label class="form-label">Email *</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-input" required>
            </div>
            <div>
                <label class="form-label">Mật khẩu mới (để trống nếu không đổi)</label>
                <input type="password" name="password" class="form-input">
            </div>
            <div>
                <label class="form-label">Xác nhận mật khẩu</label>
                <input type="password" name="password_confirmation" class="form-input">
            </div>
            <div>
                <label class="form-label">Vai trò *</label>
                <select name="role" class="form-input">
                    <option value="student" @selected($user->hasRole('student'))>Sinh viên</option>
                    <option value="lecturer" @selected($user->hasRole('lecturer'))>Giảng viên</option>
                    <option value="admin" @selected($user->hasRole('admin'))>Admin</option>
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="btn-primary">Cập nhật</button>
                <a href="{{ route('admin.users.index') }}" class="btn-secondary">Huỷ</a>
            </div>
        </form>
    </div>
</div>
@endsection
