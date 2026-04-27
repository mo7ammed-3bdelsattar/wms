<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name', 'description', 'sku', 'category_id', 'quantity'];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeFilter($query)
    {
        if (request()->has('category_id')) {
            $query->where('category_id', request()->category_id);
        }
        if (request()->has('search')) {
            $query->whereAny(['name', 'description', 'sku'], 'like', '%' . request()->search . '%');
        }
    }   
    
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
