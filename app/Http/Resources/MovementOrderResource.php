<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovementOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'notes' => $this->notes,
            'seller' => new PartnerResource($this->whenLoaded('seller')),
            'buyer' => new PartnerResource($this->whenLoaded('buyer')),
            'supplier' => new PartnerResource($this->whenLoaded('supplier')),
            'movement_type' => new MovementTypeResource($this->whenLoaded('movementType')),
            'reason' => new ReasonResource($this->whenLoaded('reason')),
            'items' => MovementOrderItemResource::collection($this->whenLoaded('items')),
        ];
    }
}
