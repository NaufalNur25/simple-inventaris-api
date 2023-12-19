<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Product
 *
 * @OA\Schema(
 *     @OA\Xml(name="Product"),
 *     description="Product Model",
 *     type="object",
 *     title="Product Model",
 *     @OA\Property(property="id", type="integer", format="int64"),
 *     @OA\Property(property="product_name", type="string"),
 *     @OA\Property(property="product_desc", type="string"),
 *     @OA\Property(property="product_release_date", type="string", format="date"),
 *     @OA\Property(property="product_acquisition_date", type="string", format="date"),
 *     @OA\Property(property="product_qty", type="integer"),
 *     @OA\Property(property="product_acquisition_cost", type="number", format="bigint"),
 *     @OA\Property(property="created_at", type="string", format="date"),
 *     @OA\Property(property="updated_at", type="string", format="date"),
 *     @OA\Property(property="deleted_at", type="string", format="date"),
 * )
 *
 * @property int $id
 * @property string $product_name
 * @property string $product_desc
 * @property string $product_release_date
 * @property string $product_acquisition_date
 * @property int $product_qty
 * @property bigint $product_acquisition_cost
 * @property string supporting_file
 * @property bigint storage
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 */
class Product extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'product_name',
        'product_desc',
        'product_release_date',
        'product_acquisition_date',
        'product_qty',
        'product_acquisition_cost',
        'supporting_file',
        'storage',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    protected $casts = [
        'product_acquisition_cost' => 'integer',
        'product_qty' => 'integer',
    ];
}
