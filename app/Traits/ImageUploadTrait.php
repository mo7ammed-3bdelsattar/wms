<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use App\Models\Image;

trait ImageUploadTrait
{
    /**
     * Upload a single image and attach it to a model (morphOne).
     */
    public function uploadSingleImage($file, $model, $folder = 'images')
    {
        // Delete old image if it exists
        if ($model->image) {
            $this->deleteImage($model->image);
        }

        $path = $file->store($folder, 'public');
        return $model->image()->create(['path' => $path]);
    }

    /**
     * Upload multiple images and attach them to a model (morphMany).
     */
    public function uploadMultipleImages($files, $model, $folder = 'images')
    {
        $images = [];
        foreach ($files as $file) {
            $path = $file->store($folder, 'public');
            $images[] = $model->images()->create(['path' => $path]);
        }
        return $images;
    }

    /**
     * Delete an image file and database record.
     */
    public function deleteImage($image)
    {
        if ($image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
            return true;
        }
        return false;
    }
}
