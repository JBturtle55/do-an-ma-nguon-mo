@extends('layouts.app')
@section('title', 'Quản lý Người dùng')
@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h1 class="text-xl font-bold text-gray-800">Quản lý Người dùng</h1>
        <a href="{{ route('admin.users.create') }}" class="btn-primary">+ Thêm người dùng</a>
    </div>
    <div class="card">
        <form method="GET" class="flex gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên hoặc email..." class="form-input flex-1 max-w-xs">
            <select name="role" class="form-input w-40">
                <option value="">Tất cả role</option>
                <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                <option value="lecturer" @selected(request('role') === 'lecturer')>Giảng viên</option>
                <option value="student" @selected(request('role') === 'student')>Sinh viên</option>
            </select>
            <button type="submit" class="btn-secondary">Lọc</button>
        </form>
    </div>
    <div class="card p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="table-th">Tên</th>
                    <th class="table-th">Email</th>
                    <th class="table-th">Vai trò</th>
                    <th class="table-th">Ngày tạo</th>
                    <th class="table-th">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                <tr class="hover:bg-gray-50">
                    <td class="table-td font-medium">{{ $user->name }}</td>
                    <td class="table-td text-gray-500">{{ $user->email }}</td>
                    <td class="table-td">
                        @foreach($user->roles as $role)
                            <x-badge :status="$role->name" />
                        @endforeach
                    </td>
                    <td class="table-td text-gray-500">{{ $user->created_at->format('d/m/Y') }}</td>
                    <td class="table-td">
                        <a href="{{ route('admin.users.edit', $user) }}" class="text-blue-600 text-xs hover:underline mr-2">Sửa</a>
                        @if($user->id !== auth()->id())
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline" onsubmit="return confirm('Xoá user này?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 text-xs hover:underline">Xoá</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-8 text-gray-400">Không có người dùng nào.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($users->hasPages())<div class="px-4 py-3 border-t">{{ $users->links() }}</div>@endif
    </div>
</div>
@endsection
