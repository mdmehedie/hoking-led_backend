<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class UploadFileName
{
    public static function make(UploadedFile $file): string
    {
        $name = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));

        if ($name === '') {
            $name = Str::lower(Str::random(8));
        }

        $extension = Str::lower($file->getClientOriginalExtension());

        return time() . '_' . $name . ($extension !== '' ? '.' . $extension : '');
    }
}
