<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CategoryCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(fn ($item) => [
                'id' => $item->id,
                'name' => $item->category_name,
                'product_count' => $item->products->count(),
                'product_total_payment' => $item->products->sum('product_acquisition_cost'),
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
            ]),
        ];
    }
}
