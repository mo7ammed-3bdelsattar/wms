<?php

namespace App\Helpers;

class FileHelper
{
    public static function get_file_path(?string $path = null, ?string $type = null)
    {
        if ($type == 'user') {
            return asset($path ? "storage/$path" : "uploads/user.png");
        } else {
            return asset($path ? "storage/$path" : "uploads/default.png");
        }
    }
}
