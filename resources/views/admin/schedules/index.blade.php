@extends('layouts.app')
@section('title', 'Lịch cố định')
@section('content')
<div class="space-y-4">
    <div class="flex justify-between items-center">
        <h1 class="text-xl font-bold text-gray-800">Lịch cố định</h1>
        <a href="{{ route('admin.schedules.create') }}" class="btn-primary">+ Thêm lịch</a>
    </div>
    <div class="card p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="table-th">Phòng</th>
                    <th class="table-th">Tiêu đề</th>
                    <th class="table-th">Loại lặp</th>
                    <th class="table-th">Thứ</th>
                    <th class="table-th">Giờ</th>
                    <th class="table-th">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @php $days = ['CN','T2','T3','T4','T5','T6','T7']; @endphp
                @forelse($schedules as $schedule)
                <tr class="hover:bg-gray-50">
                    <td class="table-td font-medium">{{ $schedule->room->name }}</td>
                    <td class="table-td">{{ $schedule->title ?? '—' }}</td>
                    <td class="table-td text-gray-500">{{ $schedule->recurring_type }}</td>
                    <td class="table-td">{{ isset($schedule->day_of_week) ? $days[$schedule->day_of_week] : '—' }}</td>
                    <td class="table-td">{{ $schedule->start_time }} – {{ $schedule->end_time }}</td>
                    <td class="table-td">
                        <a href="{{ route('admin.schedules.edit', $schedule) }}" class="text-blue-600 text-xs hover:underline mr-2">Sửa</a>
                        <form method="POST" action="{{ route('admin.schedules.destroy', $schedule) }}" class="inline" onsubmit="return confirm('Xoá lịch này?')">
                            @csrf @method('DELETE')
                            <button class="text-red-500 text-xs hover:underline">Xoá</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-8 text-gray-400">Chưa có lịch cố định nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
