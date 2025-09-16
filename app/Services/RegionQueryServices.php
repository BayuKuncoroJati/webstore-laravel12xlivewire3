<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\RegionData;
use App\Models\Region;
use Spatie\LaravelData\DataCollection;

class RegionQueryServices
{
    public function searchRegionByName(string $keywords, int $limit = 5) : DataCollection
    {
        $regions = Region::where('type', 'village')
        ->where(function($query) use ($keywords) {

            $query->where('name', 'like', "%$keywords%")
            ->orWhere('postal_code', 'LIKE', "$keywords")
            ->orWhereHas('parent', function($query) use ($keywords) {
                $query->where('name', 'LIKE', "%$keywords%");
            })
            ->orWhereHas('parent.parent', function($query) use ($keywords) {
                $query->where('name', 'LIKE', "%$keywords%");
            })
            ->orWhereHas('parent.parent.parent', function($query) use ($keywords) {
                $query->where('name', 'LIKE', "%$keywords%");
            });

        })->with(['parent.parent.parent'])
        ->limit($limit)
        ->get();

        return new DataCollection(RegionData::class, $regions);
    }

    public function searchRegionByCode(string $code) : RegionData
    {
        return RegionData::fromModel(
            Region::where('code', $code)->first()
        );
    }
}