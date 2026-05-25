@extends('layouts.app')
@section('title', 'Quản lý Thiết bị')
@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h1 class="text-xl font-bold text-gray-800">Quản lý Thiết bị</h1>
        <a href="{{ route('admin.equipment.create') }}" class="btn-primary">+ Thêm thiết bị</a>
    </div>
    <div class="card p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="table-th">Tên thiết bị</th>
                    <th class="table-th">Danh mục</th>
                    <th class="table-th">Phòng</th>
                    <th class="table-th">Số lượng</th>
                    <th class="table-th">Trạng thái</th>
                    <th class="table-th">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($equipment as $equip)
                <tr class="hover:bg-gray-50">
                    <td class="table-td font-medium">{{ $equip->name }}</td>
                    <td class="table-td text-gray-500">{{ $equip->category->name }}</td>
                    <td class="table-td text-gray-500">{{ $equip->room?->name ?? '—' }}</td>
                    <td class="table-td">{{ $equip->quantity }}</td>
                    <td class="table-td"><x-badge :status="$equip->status"/></td>
                    <td class="table-td">
                        <a href="{{ route('admin.equipment.edit', $equip) }}" class="text-blue-600 text-xs hover:underline mr-2">Sửa</a>
                        <form method="POST" action="{{ route('admin.equipment.destroy', $equip) }}" class="inline" onsubmit="return confirm('Xoá thiết bị?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 text-xs hover:underline">Xoá</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-8 text-gray-400">Chưa có thiết bị nào.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($equipment->hasPages())<div class="px-4 py-3 border-t">{{ $equipment->links() }}</div>@endif
    </div>
</div>
@endsection
