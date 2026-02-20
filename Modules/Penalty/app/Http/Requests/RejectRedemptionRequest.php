<?php

namespace Modules\Penalty\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RejectRedemptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [];
    }
}
