<?php

namespace App\Http\Requests;

use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;

class AvailableRoomsRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true; // No authentication required as per requirements
    }

    public function rules(): array
    {
        return [
            'date' => 'required|date_format:Y-m-d',
            'start_time' => [
                'required',
                'date_format:H:i',
                function ($attribute, $value, $fail) {
                    $time = Carbon::parse($value);
                    if ($time->lt('09:00')) {
                        $fail('The start time must be after 9:00 AM.');
                    }
                },
            ],
            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
                function ($attribute, $value, $fail) {
                    $time = Carbon::parse($value);
                    if ($time->gt('18:00')) {
                        $fail('The end time must be before 6:00 PM.');
                    }
                },
            ],
        ];
    }
}
