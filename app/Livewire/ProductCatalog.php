<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use App\Data\ProductData;

class ProductCatalog extends Component
{
    public function render()
    {
        $result = Product::paginate(8); // ORM / Database Query (Connect to database)
        $products = ProductData::collect($result); // Passing & Processing DTO
        return view('livewire.product-catalog', compact('products')); // Presentation
    }
}
