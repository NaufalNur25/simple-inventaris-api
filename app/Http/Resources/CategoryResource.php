<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->category_name,
            'product_count' => $this->products->count(),
            'product_total_payment' => $this->products->sum('product_acquisition_cost'),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
