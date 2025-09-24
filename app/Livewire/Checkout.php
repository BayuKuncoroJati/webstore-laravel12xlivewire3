<?php

namespace App\Livewire;

use App\Data\CartData;
use Livewire\Component;
use App\Data\RegionData;
use App\Data\ShippingData;
use Illuminate\Support\Number;
use Illuminate\Support\Facades\Gate;
use App\Services\RegionQueryServices;
use App\Contract\CartServiceInterface;
use Spatie\LaravelData\DataCollection;
use App\Services\ShippingMethodService;
use Illuminate\Support\Collection;

class Checkout extends Component
{

    public array $data = [
        'full_name' => null,
        'email' => null,
        'phone' => null,
        'shipping_line' => null,
        'destination_region_code' => null,
    ];

    public array $region_selector = [
        'keywords' => null,
        'region_selected',
        null,
    ];

    public array $shipping_selector = [
        'shipping_method' => null,
    ];

    public array $summaries = [
        'sub_total' => 0,
        'sub_total_formatted' => '-',
        'shipping_total' => 0,
        'shipping_total_formatted' => '-',
        'grand_total' => 0,
        'grand_total_formatted' => '-',
    ];

    public function mount()
    {
        if (!Gate::inspect('is_stock_available')->allowed()) {
            return to_route('cart');
        }
        $this->calculateTotal();
    }

    public function rules()
    {
        return [
            'data.full_name'    => ['required', 'min:3', 'max:255'],
            'data.email'        => ['required', 'email:dns', 'max:255'],
            'data.phone'        => ['required', 'min:7', 'max:13'],
            'data.shipping_line' => ['required', 'min10', 'max:255'],
            'data.destination_region_code' => ['required'],

        ];
    }

    public function calculateTotal()
    {
        data_set($this->summaries, 'sub_total', $this->cart->total);
        data_set($this->summaries, 'sub_total_formatted', $this->cart->total_formatted);

        $shipping_cost = 0;
        data_set($this->summaries, 'shipping_total', $shipping_cost);
        data_set($this->summaries, 'shipping_total_formatted', Number::currency($shipping_cost));

        $grand_total = $this->cart->total + $shipping_cost;
        data_set($this->summaries, 'grand_total', $grand_total);
        data_set($this->summaries, 'grand_total_formatted', Number::currency($grand_total));
    }

    public function getCartProperty(CartServiceInterface $cart): CartData
    {
        return $cart->all();
    }

    public function getRegionsProperty(RegionQueryServices $query_service): DataCollection
    {

        if (!data_get($this->region_selector, 'keywords')) {
            return new DataCollection(RegionData::class, []);
        }

        return $query_service->searchRegionByName(
            data_get($this->region_selector, 'keywords',)
        );
    }

    public function getRegionProperty(RegionQueryServices $query_service): ?RegionData
    {
        $region_selected = data_get($this->region_selector, 'region_selected');
        if (!$region_selected) {
            return null;
        }

        return $query_service->searchRegionByCode($region_selected);
    }

    public function updatedRegionSelectorRegionSelected($value)
    {
        data_set($this->data, 'destination_region_code', $value);
    }

    /** @return DataCollection<ShippingData> */
    public function getShippingMethodsProperty(
        RegionQueryServices $region_query,
        ShippingMethodService $shipping_service
    ) : DataCollection|Collection {
        if (! data_get($this->data, 'destination_region_code')) {
            return new DataCollection(ShippingData::class, []);
        }

        $origin_code = config('shipping.shipping_origin_code');

        return $shipping_service->getShippingMethods(
            $region_query->searchRegionByCode($origin_code),
            $region_query->searchRegionByCode($this->data['destination_region_code']),
            $this->cart,
        )->toCollection()->groupBy('service');
    }

    public function placeAnOrder()
    {
        $this->validate();

        dd($this->data);
    }

    public function render()
    {
        return view('livewire.checkout', [
            'cart' => $this->cart
        ]);
    }
}
