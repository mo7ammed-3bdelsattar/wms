<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\FileHelper;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'products_count' => $this->whenCounted('products'),
            'image' => FileHelper::get_file_path($this->relationLoaded('image') && $this->image ? $this->image->path : null, 'category'),
        ];
    }
}
