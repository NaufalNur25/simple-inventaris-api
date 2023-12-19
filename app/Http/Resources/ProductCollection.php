<?php

namespace App\Http\Resources;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProductCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($item) {
                return [
                    'id' => $item->id,
                    'categories' => CategoryResource::collection($item->categories)->map(fn ($category) => ['id' => $category->id, 'name' => $category->category_name]),
                    'name' => $item->product_name,
                    'description' => $item->product_desc,
                    'qty' => $item->product_qty,
                    'acquisition_cost' => $item->product_acquisition_cost,
                    'release_date' => $item->product_release_date,
                    'acquisition_date' => $item->product_acquisition_date,
                    'supporting_file' => new AttachmentResource(Attachment::find($item->storage)),
                    'created_at' => $item->created_at,
                    'updated_at' => $item->updated_at,
                ];
            }),
        ];
    }
}
