<?php

namespace App\Http\Requests;

class ReasonRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $reasonId = $this->reason ? $this->reason->id : null;
        return [
            'name' => 'required|string|max:255|unique:reasons,name,' . $reasonId,
            'description' => 'nullable|string',
        ];
    }
}
