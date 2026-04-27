<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MovementOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'movement_type_id' => 'required|exists:movement_types,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'nullable|exists:warehouses,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'buyer_id' => 'nullable|exists:users,id',
            'seller_id' => 'nullable|exists:users,id',
            'reason_id' => 'nullable|exists:reasons,id',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }
}
