@extends('layouts.app')
@section('title', 'Hồ sơ cá nhân')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <h1 class="text-xl font-bold text-gray-800">Hồ sơ cá nhân</h1>

    {{-- Update profile info --}}
    <div class="card space-y-4">
        <h2 class="font-semibold text-gray-700">Thông tin cá nhân</h2>

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf @method('PATCH')

            <div>
                <label class="form-label">Họ tên</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                       class="form-input" required autofocus>
                @error('name')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}"
                       class="form-input" required>
                @error('email')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="btn-primary">Lưu thay đổi</button>
                @if(session('status') === 'profile-updated')
                    <span x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2000)"
                          class="text-sm text-green-600">Đã lưu.</span>
                @endif
            </div>
        </form>
    </div>

    {{-- Update password --}}
    <div class="card space-y-4">
        <h2 class="font-semibold text-gray-700">Đổi mật khẩu</h2>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
            @csrf @method('PUT')

            <div>
                <label class="form-label">Mật khẩu hiện tại</label>
                <input type="password" name="current_password" class="form-input" autocomplete="current-password">
                @error('current_password', 'updatePassword')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="form-label">Mật khẩu mới</label>
                <input type="password" name="password" class="form-input" autocomplete="new-password">
                @error('password', 'updatePassword')
                    <p class="form-error">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="form-label">Xác nhận mật khẩu mới</label>
                <input type="password" name="password_confirmation" class="form-input" autocomplete="new-password">
            </div>

            <div class="flex items-center gap-4">
                <button type="submit" class="btn-primary">Cập nhật mật khẩu</button>
                @if(session('status') === 'password-updated')
                    <span x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2000)"
                          class="text-sm text-green-600">Đã cập nhật.</span>
                @endif
            </div>
        </form>
    </div>

    {{-- Delete account --}}
    <div class="card space-y-4 border border-red-100" x-data="{ confirm: false }">
        <h2 class="font-semibold text-red-600">Xoá tài khoản</h2>
        <p class="text-sm text-gray-500">Sau khi xoá, toàn bộ dữ liệu sẽ bị xoá vĩnh viễn và không thể khôi phục.</p>

        <button type="button" @click="confirm = true" class="btn-secondary text-red-600 border-red-300 hover:bg-red-50">
            Xoá tài khoản
        </button>

        <div x-show="confirm" x-cloak class="border-t pt-4 space-y-3">
            <p class="text-sm font-medium text-gray-700">Nhập mật khẩu để xác nhận:</p>
            <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-3">
                @csrf @method('DELETE')
                <input type="password" name="password" class="form-input w-64"
                       placeholder="Mật khẩu hiện tại" required>
                @error('password', 'userDeletion')
                    <p class="form-error">{{ $message }}</p>
                @enderror
                <div class="flex gap-3">
                    <button type="submit" class="btn-primary bg-red-600 hover:bg-red-700">Xác nhận xoá</button>
                    <button type="button" @click="confirm = false" class="btn-secondary">Huỷ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
