<?php

namespace Modules\Penalty\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePenaltyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|integer|exists:users,id',
            'rule_id' => 'required|integer|exists:penalty_rules,id',
            'points' => 'nullable|integer|min:1',
            'description' => 'nullable|string',
            'evidences' => 'nullable|array',
            'evidences.*' => 'required|string|max:2048',
        ];
    }
}
