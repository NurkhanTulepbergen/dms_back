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
            'user_id' => 'nullable|integer|exists:users,id|required_without:room_id|prohibited_with:room_id',
            'room_id' => 'nullable|integer|exists:rooms,id|required_without:user_id|prohibited_with:user_id',
            'rule_id' => 'required|integer|exists:penalty_rules,id',
            'points' => 'nullable|integer|min:1|max:10',
            'description' => 'nullable|string',
            'evidences' => 'nullable|array',
            'evidences.*' => 'required|file|image|max:5120',
            'evidence_paths' => 'nullable|array',
            'evidence_paths.*' => 'required|string|max:2048',
        ];
    }
}
