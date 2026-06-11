<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequestFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isCustomer();
    }

    public function rules(): array
    {
        return [
            'title'          => ['required', 'string', 'max:255'],
            'description'    => ['required', 'string', 'min:20', 'max:2000'],
            'service_id'     => ['nullable', 'exists:services,id'],
            'address'        => ['required', 'string', 'max:500'],
            'city'           => ['required', 'string', 'max:100'],
            'pincode'        => ['required', 'string', 'max:10'],
            'latitude'       => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'      => ['nullable', 'numeric', 'between:-180,180'],
            'budget_min'     => ['nullable', 'numeric', 'min:0'],
            'budget_max'     => ['nullable', 'numeric', 'min:0', 'gte:budget_min'],
            'preferred_date' => ['nullable', 'date', 'after_or_equal:today'],
            'preferred_time' => ['nullable', 'date_format:H:i'],
        ];
    }

    public function messages(): array
    {
        return [
            'description.min'        => 'Please describe your issue in at least 20 characters so we can find the right expert.',
            'budget_max.gte'         => 'Maximum budget must be greater than or equal to minimum budget.',
            'preferred_date.after_or_equal' => 'Preferred date cannot be in the past.',
        ];
    }
}
