@extends('layouts.app')
@section('title', 'Báo cáo thống kê')

@section('content')
<div class="space-y-6">
    <h1 class="text-xl font-bold text-gray-800">Báo cáo thống kê</h1>

    {{-- Date range form --}}
    <div class="card">
        <form method="GET" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="form-label">Từ ngày</label>
                <input type="date" name="from" value="{{ $from->toDateString() }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Đến ngày</label>
                <input type="date" name="to" value="{{ $to->toDateString() }}" class="form-input">
            </div>
            <button type="submit" class="btn-primary">Tạo báo cáo</button>
            <a href="{{ route('admin.reports.export', ['from' => $from->toDateString(), 'to' => $to->toDateString(), 'type' => 'utilization']) }}"
               class="btn-secondary">
                Xuất CSV
            </a>
        </form>
    </div>

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        @foreach([
            ['Tổng booking', $summary['total'], 'text-gray-800'],
            ['Chờ duyệt', $summary['pending'], 'text-yellow-600'],
            ['Đã duyệt', $summary['approved'], 'text-green-600'],
            ['Từ chối', $summary['rejected'], 'text-red-600'],
            ['Đã huỷ', $summary['cancelled'], 'text-gray-500'],
        ] as [$label, $value, $color])
        <div class="card text-center">
            <div class="text-2xl font-bold {{ $color }}">{{ $value }}</div>
            <div class="text-xs text-gray-500 mt-1">{{ $label }}</div>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Room utilization --}}
        <div class="card">
            <h2 class="font-semibold text-gray-700 mb-4">Tỷ lệ sử dụng phòng</h2>
            @forelse($utilization as $row)
            <div class="mb-3">
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-700 font-medium truncate">{{ $row['name'] }}</span>
                    <span class="text-gray-500 ml-2 flex-shrink-0">{{ $row['utilization_pct'] }}%</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="bg-blue-500 h-2 rounded-full transition-all"
                         style="width: {{ min($row['utilization_pct'], 100) }}%"></div>
                </div>
            </div>
            @empty
            <p class="text-gray-400 text-sm">Không có dữ liệu.</p>
            @endforelse
        </div>

        {{-- Top users --}}
        <div class="card">
            <h2 class="font-semibold text-gray-700 mb-4">Top người dùng</h2>
            <div class="space-y-2">
                @forelse($topUsers as $i => $user)
                <div class="flex items-center gap-3 text-sm">
                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-700 text-xs font-semibold flex items-center justify-center">
                        {{ $i + 1 }}
                    </span>
                    <span class="flex-1 text-gray-700">{{ $user->name }}</span>
                    <span class="text-gray-500 font-medium">{{ $user->count }} booking</span>
                </div>
                @empty
                <p class="text-gray-400 text-sm">Không có dữ liệu.</p>
                @endforelse
            </div>
        </div>

        {{-- Equipment usage --}}
        <div class="card lg:col-span-2">
            <h2 class="font-semibold text-gray-700 mb-4">Sử dụng thiết bị theo danh mục</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @forelse($equipUsage as $row)
                <div class="text-center p-3 bg-purple-50 rounded-lg">
                    <div class="text-xl font-bold text-purple-600">{{ $row->booking_count }}</div>
                    <div class="text-xs text-gray-500 mt-1">{{ $row->category }}</div>
                </div>
                @empty
                <p class="col-span-4 text-gray-400 text-sm">Không có dữ liệu.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
