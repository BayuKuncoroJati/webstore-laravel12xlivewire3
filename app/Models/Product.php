<?php

namespace App\Models;

use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\Tags\HasTags;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Console\Concerns\InteractsWithIO;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Product extends Model Implements HasMedia
{
    use InteractsWithMedia, HasTags;

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('cover')
        ->fit(Fit::Contain, 300, 300)
        ->nonQueued();
    }
}
