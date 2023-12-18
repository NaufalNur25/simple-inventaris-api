<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryCollection;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CategoryController extends Controller
{
    /**
     * @OA\Get(
     *      path="/category",
     *      tags={"Category"},
     *      summary="List of Category",
     *
     *      @OA\Parameter(in="query", required=false, name="filter[category_name]", @OA\Schema(type="string"), example=""),
     *      @OA\Parameter(in="query", required=false, name="sort", @OA\Schema(type="string"), example=""),
     *      @OA\Parameter(in="query", required=false, name="page", @OA\Schema(type="string"), example=""),
     *      @OA\Parameter(in="query", required=false, name="rows", @OA\Schema(type="string"), example=""),
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *      ),
     * )
     */
    public function index(Request $request)
    {
        $rows = 5;
        if ($request->filled('rows')) {
            $rows = $request->rows;
        }

        $perPage = $request->query('per_page', $rows);

        $categories = QueryBuilder::for(Category::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                'category_name',
            ])
            ->allowedSorts([
                'id',
                'category_name',
            ])
            ->paginate($perPage)
            ->appends($request->query());

        return new CategoryCollection($categories);
    }

    /**
     * @OA\Post(
     *      path="/category",
     *      tags={"Category"},
     *      summary="Store Category",
     *
     *      @OA\RequestBody(
     *         description="Body",
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *              @OA\Property(property="category_name", ref="#/components/schemas/Category/properties/category_name"),
     *
     *         ),
     *      ),
     *
     *      @OA\Response(
     *          response=201,
     *          description="success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="true"),
     *              @OA\Property(property="message", type="string", example="Data sukses disimpan."),
     *              @OA\Property(property="data", ref="#/components/schemas/Category"),
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response="422",
     *          description="error",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="category_name field is required."),
     *              @OA\Property(
     *                  property="errors",
     *                  type="array",
     *
     *                  @OA\Items(
     *
     *                      @OA\Property(property="category_name", type="array", @OA\Items(example={"category_name field is required."}))
     *                  )
     *              ),
     *          ),
     *      ),
     * )
     */
    public function store(CategoryRequest $request)
    {
        $category = Category::create($request->validated());

        return $this->sendSuccess(new CategoryResource($category), 'Data berhasil disimpan.', 201);
    }

    /**
     * @OA\Get(
     *      path="/category/{category}",
     *      tags={"Category"},
     *      summary="Category details",
     *
     *      @OA\Parameter(in="path", required=true, name="category", @OA\Schema(type="integer"), description="category_id"),
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *      ),
     * )
     */
    public function show(Category $category)
    {
        return $this->sendSuccess(new CategoryResource($category), 'Data berhasil ditampilkan.');
    }

    /**
     * @OA\Put(
     *      path="/category/{category}",
     *      tags={"Category"},
     *      summary="Update of Category",
     *
     *      @OA\Parameter(in="path", required=true, name="category", @OA\Schema(type="integer"), description="category_id"),
     *
     *      @OA\RequestBody(
     *         description="Body",
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *              @OA\Property(property="category_name", ref="#/components/schemas/Category/properties/category_name"),
     *
     *         ),
     *      ),
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="success", type="boolean", example="true"),
     *              @OA\Property(property="message", type="string", example="Data sukses disimpan."),
     *              @OA\Property(property="data", ref="#/components/schemas/Category"),
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response="422",
     *          description="error",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="category_name field is required."),
     *              @OA\Property(
     *                  property="errors",
     *                  type="array",
     *
     *                  @OA\Items(
     *
     *                      @OA\Property(property="category_name", type="array", @OA\Items(example={"category_name field is required."}))
     *                  )
     *              ),
     *          ),
     *      ),
     * )
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return $this->sendSuccess(new CategoryResource($category), 'Data sukses disimpan.');
    }

    /**
     * @OA\Delete(
     *      path="/category/{category}",
     *      tags={"Category"},
     *      summary="Category Removal",
     *
     *      @OA\Parameter(in="path", required=true, name="category", @OA\Schema(type="integer"), description="category_id"),
     *
     *      @OA\Response(
     *          response=204,
     *          description="",
     *      ),
     * )
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return $this->sendSuccess([], null, 204);
    }
}
