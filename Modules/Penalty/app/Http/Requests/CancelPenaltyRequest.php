<?php

namespace Modules\Penalty\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CancelPenaltyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'description' => 'nullable|string|max:1000',
        ];
    }
}
