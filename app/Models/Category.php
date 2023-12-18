<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Category
 *
 * @OA\Schema(
 *
 *      @OA\Xml(name="Category"),
 *      description="Category Model",
 *      type="object",
 *      title="Category Model",
 *
 *      @OA\Property(property="id", type="int"),
 *      @OA\Property(property="category_name", type="string"),
 *      @OA\Property(property="created_at", type="date"),
 *      @OA\Property(property="updated_at", type="date"),
 *      @OA\Property(property="deleted_at", type="date"),
 * )
 *
 * @property int id
 * @property string category_name
 * @property date created_at
 * @property date updated_at
 * @property date deleted_at
 */
class Category extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_name',
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}
