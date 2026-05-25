<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'         => ['required', 'string', 'max:255'],
            'bookable_type' => ['required', 'string', 'in:App\Models\Room,App\Models\Equipment'],
            'bookable_id'   => ['required', 'integer', 'min:1'],
            'start_time'    => ['required', 'date', 'after:now'],
            'end_time'      => ['required', 'date', 'after:start_time'],
            'purpose'       => ['nullable', 'string', 'max:1000'],
            'notes'         => ['nullable', 'string', 'max:500'],
            'equipment'     => ['nullable', 'array'],
            'equipment.*.id'       => ['required_with:equipment', 'integer', 'exists:equipment,id'],
            'equipment.*.quantity' => ['required_with:equipment', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'start_time.after' => 'Thời gian bắt đầu phải sau thời điểm hiện tại.',
            'end_time.after'   => 'Thời gian kết thúc phải sau thời gian bắt đầu.',
        ];
    }
}
