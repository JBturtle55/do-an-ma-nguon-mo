@extends('layouts.app')
@section('title', 'Bảo trì & Sự cố')
@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h1 class="text-xl font-bold text-gray-800">Bảo trì & Sự cố</h1>
        <a href="{{ route('admin.maintenance.create') }}" class="btn-primary">+ Báo cáo sự cố</a>
    </div>
    <div class="card">
        <form method="GET" class="flex gap-3">
            <select name="status" class="form-input w-40">
                <option value="">Tất cả</option>
                <option value="open" @selected(request('status') === 'open')>Mới báo</option>
                <option value="in_progress" @selected(request('status') === 'in_progress')>Đang xử lý</option>
                <option value="resolved" @selected(request('status') === 'resolved')>Đã giải quyết</option>
            </select>
            <button type="submit" class="btn-secondary">Lọc</button>
        </form>
    </div>
    <div class="card p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="table-th">Đối tượng</th>
                    <th class="table-th">Mô tả</th>
                    <th class="table-th">Người báo</th>
                    <th class="table-th">Trạng thái</th>
                    <th class="table-th">Ngày báo</th>
                    <th class="table-th">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50">
                    <td class="table-td font-medium">{{ $log->loggable?->name ?? '—' }}</td>
                    <td class="table-td text-gray-600 max-w-xs">
                        <a href="{{ route('admin.maintenance.show', $log) }}" class="hover:underline text-gray-700 line-clamp-1">{{ $log->description }}</a>
                    </td>
                    <td class="table-td text-gray-500">{{ $log->reporter->name }}</td>
                    <td class="table-td"><x-badge :status="$log->status"/></td>
                    <td class="table-td text-gray-500">{{ $log->created_at->format('d/m/Y') }}</td>
                    <td class="table-td space-x-2">
                        @if($log->status === 'open')
                        <form method="POST" action="{{ route('admin.maintenance.progress', $log) }}" class="inline"
                              onsubmit="return confirm('Chuyển sang đang xử lý?')">
                            @csrf @method('PATCH')
                            <button class="text-blue-600 text-xs hover:underline">Đang xử lý</button>
                        </form>
                        @endif
                        @if($log->status !== 'resolved')
                        <form method="POST" action="{{ route('admin.maintenance.resolve', $log) }}" class="inline"
                              onsubmit="return confirm('Đánh dấu đã giải quyết xong?')">
                            @csrf @method('PATCH')
                            <button class="text-green-600 text-xs hover:underline">Đánh dấu xong</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-8 text-gray-400">Không có sự cố nào.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($logs->hasPages())<div class="px-4 py-3 border-t">{{ $logs->links() }}</div>@endif
    </div>
</div>
@endsection
