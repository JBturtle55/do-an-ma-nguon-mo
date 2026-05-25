@extends('layouts.app')
@section('title', 'Sửa lịch cố định')
@section('content')
<div class="max-w-lg mx-auto space-y-4">
    <h1 class="text-xl font-bold text-gray-800">Sửa lịch cố định</h1>
    <div class="card">
        <form method="POST" action="{{ route('admin.schedules.update', $schedule) }}" class="space-y-4"
              x-data="{ recurringType: @json(old('recurring_type', $schedule->recurring_type)) }">
            @csrf @method('PUT')
            <div><label class="form-label">Phòng *</label><select name="room_id" class="form-input" required>@foreach($rooms as $room)<option value="{{ $room->id }}" @selected(old('room_id', $schedule->room_id) == $room->id)>{{ $room->name }}</option>@endforeach</select></div>
            <div><label class="form-label">Tiêu đề</label><input type="text" name="title" value="{{ old('title', $schedule->title) }}" class="form-input"></div>
            <div>
                <label class="form-label">Loại lặp *</label>
                <select name="recurring_type" x-model="recurringType" class="form-input">
                    <option value="none">Một lần</option>
                    <option value="weekly">Hàng tuần</option>
                    <option value="daily">Hàng ngày</option>
                </select>
            </div>
            <div x-show="recurringType === 'weekly'" style="display:none">
                <label class="form-label">Thứ trong tuần *</label>
                <select name="day_of_week" class="form-input">
                    <option value="">--</option>
                    @foreach(['Chủ nhật','Thứ 2','Thứ 3','Thứ 4','Thứ 5','Thứ 6','Thứ 7'] as $i => $day)
                        <option value="{{ $i }}" @selected(old('day_of_week', $schedule->day_of_week) == $i)>{{ $day }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="form-label">Giờ bắt đầu *</label><input type="time" name="start_time" value="{{ old('start_time', $schedule->start_time) }}" class="form-input" required></div>
                <div><label class="form-label">Giờ kết thúc *</label><input type="time" name="end_time" value="{{ old('end_time', $schedule->end_time) }}" class="form-input" required></div>
            </div>
            <div class="flex gap-3"><button type="submit" class="btn-primary">Cập nhật</button><a href="{{ route('admin.schedules.index') }}" class="btn-secondary">Huỷ</a></div>
        </form>
    </div>
</div>
@endsection
