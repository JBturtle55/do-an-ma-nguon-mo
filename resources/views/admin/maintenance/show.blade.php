@extends('layouts.app')
@section('title', 'Chi tiết sự cố')
@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.maintenance.index') }}" class="text-sm text-gray-500 hover:text-gray-700">← Danh sách sự cố</a>
        <x-badge :status="$log->status"/>
    </div>

    <div class="card space-y-4">
        <h1 class="text-xl font-bold text-gray-800">Chi tiết sự cố</h1>

        <dl class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <dt class="text-gray-500 mb-1">Đối tượng</dt>
                <dd class="font-medium">{{ $log->loggable?->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 mb-1">Loại</dt>
                <dd class="font-medium">{{ class_basename($log->loggable_type) }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 mb-1">Người báo cáo</dt>
                <dd class="font-medium">{{ $log->reporter->name }}</dd>
            </div>
            <div>
                <dt class="text-gray-500 mb-1">Ngày báo</dt>
                <dd class="font-medium">{{ $log->created_at->format('d/m/Y H:i') }}</dd>
            </div>
            @if($log->resolved_at)
            <div>
                <dt class="text-gray-500 mb-1">Ngày giải quyết</dt>
                <dd class="font-medium text-green-600">{{ $log->resolved_at->format('d/m/Y H:i') }}</dd>
            </div>
            @endif
        </dl>

        <div>
            <dt class="text-sm text-gray-500 mb-1">Mô tả sự cố</dt>
            <dd class="bg-gray-50 rounded-lg p-4 text-sm text-gray-800 leading-relaxed">{{ $log->description }}</dd>
        </div>

        @if($log->status !== 'resolved')
        <div class="flex gap-3 pt-2 border-t">
            @if($log->status === 'open')
            <form method="POST" action="{{ route('admin.maintenance.progress', $log) }}" onsubmit="return confirm('Chuyển sang đang xử lý?')">
                @csrf @method('PATCH')
                <button class="btn-secondary">Đang xử lý</button>
            </form>
            @endif
            <form method="POST" action="{{ route('admin.maintenance.resolve', $log) }}" onsubmit="return confirm('Đánh dấu đã giải quyết xong?')">
                @csrf @method('PATCH')
                <button class="btn-primary">Đánh dấu xong</button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
