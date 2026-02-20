<?php

namespace Modules\Penalty\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePenaltyRedemptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'event_type' => 'required|string|max:255',
            'description' => 'required|string',
            'file_path' => 'nullable|string|max:2048',
        ];
    }
}
