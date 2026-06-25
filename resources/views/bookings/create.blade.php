@extends('layouts.app')
@section('title', 'Đặt lịch mới')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <h1 class="text-xl font-bold text-gray-800">Đặt lịch mới</h1>

    <div class="card" x-data="bookingForm()" x-init="init()">
        <form method="POST" action="{{ route('bookings.store') }}" @submit="submitForm($event)">
            @csrf

            {{-- Hidden inputs for bookable (controlled by Alpine state) --}}
            <input type="hidden" name="bookable_type" :value="bookableType">
            <input type="hidden" name="bookable_id" :value="bookableId">

            {{-- Loại đối tượng đặt --}}
            <div class="mb-4">
                <label class="form-label">Loại đặt lịch</label>
                <select x-model="bookableType" @change="resetBookable()" class="form-input">
                    <option value="App\Models\Room">Phòng lab / Phòng học</option>
                    <option value="App\Models\Equipment">Thiết bị</option>
                </select>
            </div>

            {{-- Chọn phòng --}}
            <div class="mb-4" x-show="bookableType === 'App\\Models\\Room'">
                <label class="form-label">Chọn phòng <span class="text-red-500">*</span></label>
                <select x-model="bookableId" @change="checkAvailability()" class="form-input">
                    <option value="">-- Chọn phòng --</option>
                    @foreach($rooms as $room)
                        <option value="{{ $room->id }}">
                            {{ $room->name }} ({{ $room->building }}, {{ $room->capacity }} chỗ)
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Chọn thiết bị --}}
            <div class="mb-4" x-show="bookableType === 'App\\Models\\Equipment'">
                <label class="form-label">Chọn thiết bị <span class="text-red-500">*</span></label>
                <select x-model="bookableId" @change="checkAvailability()" class="form-input">
                    <option value="">-- Chọn thiết bị --</option>
                    @foreach($equipment as $equip)
                        <option value="{{ $equip->id }}">
                            {{ $equip->name }} ({{ $equip->category->name }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Tiêu đề --}}
            <div class="mb-4">
                <label class="form-label">Tiêu đề <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" placeholder="VD: Thực hành Điện tử - Nhóm 3"
                       class="form-input" required>
                @error('title')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            {{-- Ngày --}}
            <div class="mb-3">
                <label class="form-label">Ngày <span class="text-red-500">*</span></label>
                <input type="date" x-model="selectedDate" @change="syncFromDate()" class="form-input w-48">
            </div>

            {{-- Chọn ca nhanh --}}
            <div class="mb-4">
                <label class="form-label">Chọn ca nhanh</label>
                <div class="grid grid-cols-5 gap-2">
                    <template x-for="slot in slots" :key="slot.label">
                        <button type="button" @click="selectSlot(slot)"
                                :style="isSlotActive(slot)
                                    ? 'border-color:#3b82f6;background:#eff6ff;color:#1d4ed8;'
                                    : 'border-color:#e5e7eb;background:#fff;color:#374151;'"
                                style="border-width:1.5px;border-style:solid;border-radius:8px;padding:8px 4px;text-align:center;cursor:pointer;transition:all .15s;">
                            <div style="font-weight:600;font-size:13px;" x-text="slot.label"></div>
                            <div style="font-size:11px;margin-top:2px;color:#6b7280;" x-text="slot.start + '–' + slot.end"></div>
                            <div style="font-size:10px;margin-top:1px;color:#9ca3af;" x-text="slot.sub"></div>
                        </button>
                    </template>
                </div>
                <p class="text-xs text-gray-400 mt-1.5">Chọn ngày trước, sau đó bấm ca để điền giờ tự động.</p>
            </div>

            {{-- Thời gian --}}
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="form-label">Bắt đầu <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="start_time" x-model="startTime"
                           @change="syncDateFromTime(); checkAvailability();" value="{{ old('start_time') }}"
                           class="form-input" required>
                    @error('start_time')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">Kết thúc <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="end_time" x-model="endTime"
                           @change="selectedSlots = []; checkAvailability();" value="{{ old('end_time') }}"
                           class="form-input" required>
                    @error('end_time')<p class="form-error">{{ $message }}</p>@enderror
                </div>
            </div>

            {{-- Availability indicator --}}
            <div x-show="availabilityChecked" class="mb-4 p-3 rounded-lg text-sm"
                 :class="isAvailable ? 'bg-green-50 border border-green-200 text-green-700' : 'bg-red-50 border border-red-200 text-red-700'">
                <template x-if="isAvailable">
                    <span>✓ Thời gian này còn trống, có thể đặt.</span>
                </template>
                <template x-if="!isAvailable">
                    <span>✗ Thời gian này đã có booking trùng. Vui lòng chọn thời gian khác.</span>
                </template>
            </div>

            {{-- Mục đích --}}
            <div class="mb-4">
                <label class="form-label">Mục đích sử dụng</label>
                <textarea name="purpose" rows="3" placeholder="Mô tả mục đích sử dụng..."
                          class="form-input">{{ old('purpose') }}</textarea>
            </div>

            {{-- Ghi chú --}}
            <div class="mb-6">
                <label class="form-label">Ghi chú</label>
                <textarea name="notes" rows="2" class="form-input">{{ old('notes') }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-primary" :disabled="availabilityChecked && !isAvailable">
                    Gửi yêu cầu đặt lịch
                </button>
                <a href="{{ route('bookings.index') }}" class="btn-secondary">Huỷ</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function bookingForm() {
    return {
        bookableType: @json(old('bookable_type', $preselect['type'] ?? 'App\Models\Room')),
        bookableId:   @json(old('bookable_id',   $preselect['id']   ?? '')),
        startTime: '',
        endTime: '',
        selectedDate: '',
        availabilityChecked: false,
        isAvailable: false,

        slots: [
            { label: 'Ca 1', sub: 'Tiết 1–3',   start: '06:45', end: '09:00' },
            { label: 'Ca 2', sub: 'Tiết 4–6',   start: '09:20', end: '11:35' },
            { label: 'Ca 3', sub: 'Tiết 7–9',   start: '12:30', end: '14:45' },
            { label: 'Ca 4', sub: 'Tiết 10–12', start: '15:05', end: '17:20' },
            { label: 'Ca 5', sub: 'Tiết 13–15', start: '18:00', end: '20:15' },
        ],
        selectedSlots: [],

        init() {
            const oldStart = '{{ old('start_time') }}';
            const oldEnd   = '{{ old('end_time') }}';
            if (oldStart) { this.startTime = oldStart; this.selectedDate = oldStart.split('T')[0]; }
            if (oldEnd)   { this.endTime = oldEnd; }
            if (this.bookableId && this.startTime && this.endTime) {
                this.checkAvailability();
            }
        },

        resetBookable() {
            this.bookableId = '';
            this.availabilityChecked = false;
        },

        selectSlot(slot) {
            if (!this.selectedDate) {
                alert('Vui lòng chọn ngày trước khi chọn ca.');
                return;
            }
            const idx = this.selectedSlots.indexOf(slot.label);
            if (idx >= 0) {
                this.selectedSlots.splice(idx, 1);
            } else {
                this.selectedSlots.push(slot.label);
            }
            this.updateTimesFromSlots();
        },

        isSlotActive(slot) {
            return this.selectedSlots.includes(slot.label);
        },

        updateTimesFromSlots() {
            if (!this.selectedDate || this.selectedSlots.length === 0) return;
            const active = this.slots.filter(s => this.selectedSlots.includes(s.label));
            const minStart = active.map(s => s.start).sort()[0];
            const maxEnd   = active.map(s => s.end).sort().reverse()[0];
            this.startTime = this.selectedDate + 'T' + minStart;
            this.endTime   = this.selectedDate + 'T' + maxEnd;
            this.checkAvailability();
        },

        // When date picker changes, re-apply selected slots (or shift existing times)
        syncFromDate() {
            if (this.selectedSlots.length > 0) {
                this.updateTimesFromSlots();
            } else {
                if (this.startTime) {
                    const t = this.startTime.split('T')[1];
                    if (t) this.startTime = this.selectedDate + 'T' + t;
                }
                if (this.endTime) {
                    const t = this.endTime.split('T')[1];
                    if (t) this.endTime = this.selectedDate + 'T' + t;
                }
                this.checkAvailability();
            }
        },

        // When user edits datetime inputs manually, clear Ca selection & sync date
        syncDateFromTime() {
            if (this.startTime) {
                this.selectedDate = this.startTime.split('T')[0] || '';
                this.selectedSlots = [];
            }
        },

        async checkAvailability() {
            if (!this.bookableId || !this.startTime || !this.endTime) return;

            const params = new URLSearchParams({
                bookable_type: this.bookableType,
                bookable_id:   this.bookableId,
                start_time:    this.startTime,
                end_time:      this.endTime,
            });

            try {
                const res = await fetch(`/api/availability/check?${params}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                this.isAvailable = data.available;
                this.availabilityChecked = true;
            } catch (e) {
                this.availabilityChecked = false;
            }
        },

        submitForm(e) {
            if (!this.bookableId || !this.startTime || !this.endTime) return;
            if (!this.availabilityChecked) {
                e.preventDefault();
                alert('Vui lòng đợi kiểm tra lịch trống trước khi gửi.');
                this.checkAvailability();
                return;
            }
            if (!this.isAvailable) {
                e.preventDefault();
                alert('Vui lòng chọn thời gian không bị trùng.');
            }
        },
    };
}
</script>
@endpush
