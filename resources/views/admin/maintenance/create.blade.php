@extends('layouts.app')
@section('title', 'Báo cáo sự cố')
@section('content')
<div class="max-w-lg mx-auto space-y-4">
    <h1 class="text-xl font-bold text-gray-800">Báo cáo sự cố mới</h1>
    <div class="card">
        <form method="POST" action="{{ route('admin.maintenance.store') }}" class="space-y-4"
              x-data="maintenanceForm()">
            @csrf

            {{-- Hidden inputs controlled by Alpine --}}
            <input type="hidden" name="loggable_type" :value="type">
            <input type="hidden" name="loggable_id" :value="loggableId">

            <div>
                <label class="form-label">Loại đối tượng *</label>
                <select x-model="type" @change="loggableId = ''" class="form-input">
                    <option value="App\Models\Room">Phòng</option>
                    <option value="App\Models\Equipment">Thiết bị</option>
                </select>
            </div>

            <div x-show="type === 'App\\Models\\Room'">
                <label class="form-label">Phòng bị sự cố *</label>
                <select x-model="loggableId" class="form-input">
                    <option value="">-- Chọn phòng --</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}">
                            {{ $room->name }} — {{ $room->building }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div x-show="type === 'App\\Models\\Equipment'">
                <label class="form-label">Thiết bị bị sự cố *</label>
                <select x-model="loggableId" class="form-input">
                    <option value="">-- Chọn thiết bị --</option>
                    @foreach($equipment as $equip)
                        <option value="{{ $equip->id }}">
                            {{ $equip->name }} ({{ $equip->category->name }})
                        </option>
                    @endforeach
                </select>
            </div>

            @error('loggable_id')<p class="form-error">{{ $message }}</p>@enderror

            <div>
                <label class="form-label">Mô tả sự cố *</label>
                <textarea name="description" rows="4" class="form-input" required
                          placeholder="Mô tả chi tiết sự cố, triệu chứng, thời điểm xảy ra...">{{ old('description') }}</textarea>
                @error('description')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-warning">Gửi báo cáo</button>
                <a href="{{ route('admin.maintenance.index') }}" class="btn-secondary">Huỷ</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function maintenanceForm() {
    return {
        type: @json(old('loggable_type', 'App\Models\Room')),
        loggableId: @json(old('loggable_id', '')),
    };
}
</script>
@endpush
