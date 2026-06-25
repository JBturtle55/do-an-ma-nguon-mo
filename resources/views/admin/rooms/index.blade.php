@extends('layouts.app')
@section('title', 'Quản lý Phòng')
@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h1 class="text-xl font-bold text-gray-800">Quản lý Phòng</h1>
        <a href="{{ route('admin.rooms.create') }}" class="btn-primary">+ Thêm phòng</a>
    </div>
    <div class="card p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="table-th">Tên phòng</th>
                    <th class="table-th">Toà nhà</th>
                    <th class="table-th">Loại</th>
                    <th class="table-th">Sức chứa</th>
                    <th class="table-th">Trạng thái</th>
                    <th class="table-th">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($rooms as $room)
                <tr class="hover:bg-gray-50">
                    <td class="table-td font-medium">{{ $room->name }}</td>
                    <td class="table-td text-gray-500">{{ $room->building }}</td>
                    <td class="table-td"><x-badge :status="$room->type"/></td>
                    <td class="table-td">{{ $room->capacity }}</td>
                    <td class="table-td"><x-badge :status="$room->status"/></td>
                    <td class="table-td">
                        <a href="{{ route('admin.rooms.show', $room) }}" class="text-gray-600 text-xs hover:underline mr-2">Xem</a>
                        <a href="{{ route('admin.rooms.edit', $room) }}" class="text-blue-600 text-xs hover:underline mr-2">Sửa</a>
                        <form method="POST" action="{{ route('admin.rooms.destroy', $room) }}" class="inline" onsubmit="return confirm('Xoá phòng này?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 text-xs hover:underline">Xoá</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-8 text-gray-400">Chưa có phòng nào.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($rooms->hasPages())<div class="px-4 py-3 border-t">{{ $rooms->links() }}</div>@endif
    </div>
</div>
@endsection
