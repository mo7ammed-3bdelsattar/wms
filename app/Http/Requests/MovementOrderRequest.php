<?php

namespace App\Http\Requests;

use App\Http\Requests\BaseFormRequest;

class MovementOrderRequest extends BaseFormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'movement_type_id' => 'required|exists:movement_types,id',
            'supplier_id' => 'nullable|exists:partners,id',
            'buyer_id' => 'nullable|exists:partners,id',
            'seller_id' => 'nullable|exists:partners,id',
            'reason_id' => 'nullable|exists:reasons,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }
}
