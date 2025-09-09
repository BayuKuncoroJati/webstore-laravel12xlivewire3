<?php

namespace App\Models;

use App\Models\Product;
use Spatie\Tags\Tag as TagsTag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

// Model Tag extend ke Tags spatie
class Tag extends TagsTag
{
    public function products() : MorphToMany
    {
        return $this->morphedByMany(Product::class, 'taggable');
    }
}
