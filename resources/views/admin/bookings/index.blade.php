@extends('layouts.app')
@section('title', 'Quản lý Booking')

@section('content')
<div class="space-y-4" x-data="{ rejectOpen: false, rejectId: null, rejectNotes: '' }"
     @keydown.escape.window="rejectOpen = false">
    <h1 class="text-xl font-bold text-gray-800">Quản lý Booking</h1>

    {{-- Filters --}}
    <div class="card">
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tên booking..." class="form-input w-48">
            <select name="status" class="form-input w-40">
                <option value="">Tất cả</option>
                @foreach(['pending' => 'Chờ duyệt', 'approved' => 'Đã duyệt', 'rejected' => 'Từ chối', 'cancelled' => 'Đã huỷ'] as $val => $label)
                    <option value="{{ $val }}" @selected(request('status') === $val)>{{ $label }}</option>
                @endforeach
            </select>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-input w-40">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-input w-40">
            <button type="submit" class="btn-secondary">Lọc</button>
            @if(request()->hasAny(['search','status','date_from','date_to']))
                <a href="{{ route('admin.bookings.index') }}" class="btn-secondary">Xoá bộ lọc</a>
            @endif
        </form>
    </div>

    <div class="card p-0 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="table-th">Tiêu đề</th>
                    <th class="table-th">Người đặt</th>
                    <th class="table-th">Đối tượng</th>
                    <th class="table-th">Thời gian</th>
                    <th class="table-th">Trạng thái</th>
                    <th class="table-th">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($bookings as $booking)
                <tr class="hover:bg-gray-50">
                    <td class="table-td font-medium">
                        <a href="{{ route('admin.bookings.show', $booking) }}" class="text-blue-600 hover:underline">
                            {{ $booking->title }}
                        </a>
                    </td>
                    <td class="table-td">{{ $booking->user->name }}</td>
                    <td class="table-td text-gray-500">{{ $booking->bookable?->name ?? '—' }}</td>
                    <td class="table-td text-gray-500 whitespace-nowrap">
                        {{ $booking->start_time->format('d/m H:i') }} – {{ $booking->end_time->format('H:i') }}
                    </td>
                    <td class="table-td"><x-badge :status="$booking->status"/></td>
                    <td class="table-td">
                        @if($booking->status === 'pending')
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('admin.bookings.approve', $booking) }}"
                                      onsubmit="return confirm('Duyệt booking này?')">
                                    @csrf @method('PATCH')
                                    <button class="text-green-600 text-xs hover:underline font-medium">Duyệt</button>
                                </form>
                                <button @click="rejectOpen = true; rejectId = {{ $booking->id }}"
                                        class="text-red-500 text-xs hover:underline font-medium">Từ chối</button>
                            </div>
                        @else
                            <a href="{{ route('admin.bookings.show', $booking) }}" class="text-blue-600 text-xs hover:underline">Chi tiết</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-8 text-gray-400">Không có booking nào.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($bookings->hasPages())
            <div class="px-4 py-3 border-t">{{ $bookings->links() }}</div>
        @endif
    </div>

    {{-- Reject modal --}}
    {{-- Backdrop --}}
    <div x-show="rejectOpen" @click="rejectOpen = false"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-black/40 z-50" style="display:none"></div>
    {{-- Panel --}}
    <div x-show="rejectOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none" style="display:none">
        <div class="bg-white rounded-xl shadow-xl pointer-events-auto" style="width:400px;max-width:calc(100vw - 2rem)" @click.stop>
            <div class="flex items-center gap-2.5 px-4 pt-4 pb-3">
                <div class="w-7 h-7 rounded-full bg-red-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <p class="font-semibold text-gray-900 text-sm">Từ chối Booking</p>
                    <p class="text-xs text-gray-400 mt-0.5">Hành động này không thể hoàn tác.</p>
                </div>
                <button @click="rejectOpen = false" class="text-gray-300 hover:text-gray-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form :action="`{{ url('/admin/bookings') }}/${rejectId}/reject`" method="POST">
                @csrf @method('PATCH')
                <div class="px-4 pb-3">
                    <label class="form-label">Lý do từ chối <span class="text-red-500">*</span></label>
                    <textarea name="notes" x-model="rejectNotes" rows="3"
                              class="form-input mt-1" placeholder="Nhập lý do từ chối..." required></textarea>
                </div>
                <div class="flex gap-2 justify-end px-4 pb-4">
                    <button type="button" @click="rejectOpen = false" class="btn-secondary">Huỷ</button>
                    <button type="submit" class="btn-danger">Xác nhận từ chối</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
