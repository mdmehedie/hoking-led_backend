<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Setting extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $fillable = ['key', 'value'];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('logo_light')->singleFile();
        $this->addMediaCollection('logo_dark')->singleFile();
        $this->addMediaCollection('favicon')->singleFile();
    }
}
