<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true; // No authentication required as per requirements
    }

    public function rules(): array
    {
        return [
            'room_id' => 'required|exists:rooms,id',
            'user_name' => 'required|string|max:255',
            'date' => 'required|date|date_format:Y-m-d',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ];
    }

    public function after(): array
    {
        return [
            function ($validator) {
                $validated = $this->validated();

                $startTime = Carbon::createFromFormat('H:i', $validated['start_time']);
                $endTime = Carbon::createFromFormat('H:i', $validated['end_time']);
                $openTime = Carbon::createFromFormat('H:i', '09:00');
                $closeTime = Carbon::createFromFormat('H:i', '18:00');

                if ($startTime->lt($openTime) || $endTime->gt($closeTime)) {
                    $validator->errors()->add('time', 'Bookings are only allowed between 9:00 AM and 6:00 PM');
                }
            }
        ];
    }
}
