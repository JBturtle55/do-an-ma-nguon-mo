@extends('layouts.app')
@section('title', 'Quản lý Người dùng')
@section('content')
<div class="space-y-4" x-data="{
    confirmOpen: false,
    confirmUrl: '',
    openConfirm(url) { this.confirmUrl = url; this.confirmOpen = true; }
}" @keydown.escape.window="confirmOpen = false">
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
                        <button type="button" class="text-red-500 text-xs hover:underline"
                                @click="openConfirm('{{ route('admin.users.destroy', $user) }}')">Xoá</button>
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

    {{-- Confirm delete modal --}}
    <div x-show="confirmOpen" @click="confirmOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/40 z-50" style="display:none"></div>
    <div x-show="confirmOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none" style="display:none">
        <div class="bg-white rounded-xl shadow-xl pointer-events-auto" style="width:360px;max-width:calc(100vw - 2rem)" @click.stop>
            <div class="flex items-center gap-2.5 px-4 pt-4 pb-3">
                <div class="w-7 h-7 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-900 text-sm">Xoá người dùng</p>
                    <p class="text-xs text-gray-400 mt-0.5">Hành động này không thể hoàn tác.</p>
                </div>
                <button @click="confirmOpen = false" class="text-gray-300 hover:text-gray-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <p class="text-xs text-gray-500 px-4 pb-4">Bạn có chắc muốn xoá người dùng này không?</p>
            <div class="flex gap-2 justify-end px-4 pb-4">
                <button type="button" @click="confirmOpen = false" class="btn-secondary">Huỷ</button>
                <form :action="confirmUrl" method="POST" @submit="confirmOpen = false">
                    @csrf
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="btn-danger">Xoá</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
