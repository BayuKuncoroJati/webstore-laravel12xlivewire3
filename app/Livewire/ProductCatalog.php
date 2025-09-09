<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Tag;
use App\Models\Product;
use Livewire\Component;
use App\Data\ProductData;
use Livewire\WithPagination;
use App\Data\ProductCollectionData;

class ProductCatalog extends Component
{
    use WithPagination;
    public $queryString = [
        'select_collections' => ['except' => []],
        'search' => ['except' => []],
        'sort_by' => ['except' => 'newest']
    ];
    public array $select_collections = [];

    public string $search = '';

    public string $sort_by = 'newest';

    public function applyFilters()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->select_collections = [];
        $this->search = '';
        $this->sort_by = 'newest';
        $this->resetPage();
    }

    public function render()
    {
        $collection_result = Tag::query()->withType('collection')->withCount('products')->get(); // Collection tags spatie
        // $result = Product::paginate(8); // ORM / Database Query (Connect to database)
        $query = Product::query();

        if ($this->search) {
            $query->where('name', 'LIKE', "%{$this->search}%");
        }

        if (!empty($this->select_collections)) {
            $query->whereHas('tags', function($query) {
                $query->whereIn('id', $this->select_collections);
            });
        } 

        switch($this->sort_by) {
            case 'latest':
                $query->oldest();
                break;
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
        }

        $products = ProductData::collect(
            $query->paginate(8)
        );
        $collections = ProductCollectionData::collect($collection_result);

        // $result = Product::paginate(8); // ORM / Database Query (Connect to database)

        // $products = ProductData::collect($result); // Passing & Processing DTO
        // $collections = ProductCollectionData::collect($collection_result);
        
        return view('livewire.product-catalog', compact('products', 'collections')); // Presentation
    }
}
