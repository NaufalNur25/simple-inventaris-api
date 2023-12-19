<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Attachment
 * @package App\Models
 *
 * @OA\Schema(
 *      @OA\Xml(name="Attachment"),
 *      description="Attachment Model",
 *      type="object",
 *      title="Attachment Model",
 *      @OA\Property(property="id", type="int"),
 *      @OA\Property(property="hash_name", type="string"),
 *      @OA\Property(property="original_name", type="string"),
 *      @OA\Property(property="size", type="string"),
 *      @OA\Property(property="mimetypes", type="string"),
 *      @OA\Property(property="service_name", type="string"),
 *      @OA\Property(property="deleted_at", type="string"),
 *      @OA\Property(property="created_by", type="int"),
 *      @OA\Property(property="updated_by", type="int"),
 *      @OA\Property(property="created_at", type="string"),
 *      @OA\Property(property="updated_at", type="string"),
 * )
 * @property int id
 * @property string hash_name
 * @property string original_name
 * @property string size
 * @property string mimetypes
 * @property string service_name
 * @property string deleted_at
 * @property int created_by
 * @property int updated_by
 * @property string created_at
 * @property string updated_at
 */
class Attachment extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
		'hash_name',
		'original_name',
		'size',
		'mimetypes',
		'service_name',
	];
}
