<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Tag;
use App\Models\Product;
use Livewire\Component;
use App\Data\ProductData;
use App\Data\ProductCollectionData;

class ProductCatalog extends Component
{
    public function render()
    {
        $collection_result = Tag::query()->withType('collection')->withCount('products')->get(); // Collection tags spatie
        $result = Product::paginate(8); // ORM / Database Query (Connect to database)

        $products = ProductData::collect($result); // Passing & Processing DTO
        $collections = ProductCollectionData::collect($collection_result);
        
        return view('livewire.product-catalog', compact('products', 'collections')); // Presentation
    }
}
