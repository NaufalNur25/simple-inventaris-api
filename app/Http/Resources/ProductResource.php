<?php

namespace App\Http\Resources;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'categories' => CategoryResource::collection($this->categories)->map(fn ($category) => ['id' => $category->id, 'name' => $category->category_name]),
            'name' => $this->product_name,
            'description' => $this->product_desc,
            'qty' => $this->product_qty,
            'acquisition_cost' => $this->product_acquisition_cost,
            'release_date' => $this->product_release_date,
            'acquisition_date' => $this->product_acquisition_date,
            'supporting_file' => new AttachmentResource(Attachment::find($this->storage)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
