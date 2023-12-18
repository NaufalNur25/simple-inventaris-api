<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Http\Resources\ProductCollection;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ProductController extends Controller
{
    /**
     * @OA\Get(
     *      path="/product",
     *      tags={"Product"},
     *      summary="List of Product",
     *
     *      @OA\Parameter(in="query", required=false, name="filter[keyword]", @OA\Schema(type="string"), example=""),
     *      @OA\Parameter(in="query", required=false, name="filter[product_qty]", @OA\Schema(type="string"), example=""),
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

        $products = QueryBuilder::for(Product::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::callback(
                    'keyword',
                    fn ($query, $value) => $query->where('product_name', 'like', '%' . $value . '%')
                        ->orWhere('product_desc', 'like', '%' . $value . '%')
                        ->orWhereHas('categories', fn ($query) => $query->where('category_name', 'like', '%' . $value . '%'))
                ),
                'product_qty',
            ])
            ->allowedSorts([
                'id',
                'product_name',
                'product_desc',
                'product_acquisition_cost',
                'product_release_date',
                'product_acquisition_date',
            ])
            ->paginate($perPage)
            ->appends($request->query());

        return new ProductCollection($products);
    }

    /**
     * @OA\Post(
     *      path="/product",
     *      tags={"Product"},
     *      summary="Store Product",
     *
     *      @OA\RequestBody(
     *         description="Body",
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *              @OA\Property(property="category_id", type="array", @OA\Items(type="integer")),
     *              @OA\Property(property="product_name", ref="#/components/schemas/Product/properties/product_name"),
     *              @OA\Property(property="product_desc", ref="#/components/schemas/Product/properties/product_desc"),
     *              @OA\Property(property="product_release_date", ref="#/components/schemas/Product/properties/product_release_date"),
     *              @OA\Property(property="product_acquisition_date", ref="#/components/schemas/Product/properties/product_acquisition_date"),
     *              @OA\Property(property="product_qty", ref="#/components/schemas/Product/properties/product_qty"),
     *              @OA\Property(property="product_acquisition_cost", ref="#/components/schemas/Product/properties/product_acquisition_cost"),
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
     *              @OA\Property(property="data", ref="#/components/schemas/Product"),
     *
     *          )
     *      ),
     *
     *      @OA\Response(
     *          response="422",
     *          description="error",
     *
     *          @OA\JsonContent(
     *
     *              @OA\Property(property="message", type="string", example="product_name field is required."),
     *              @OA\Property(
     *                  property="errors",
     *                  type="array",
     *
     *                  @OA\Items(
     *
     *                      @OA\Property(property="product_name", type="array", @OA\Items(example={"product_name field is required."})),
     *                      @OA\Property(property="product_acquisition_date", type="array", @OA\Items(example={"product_acquisition_date field is required."})),
     *                      @OA\Property(property="product_qty", type="array", @OA\Items(example={"product_qty field is required."})),
     *                      @OA\Property(property="product_acquisition_cost", type="array", @OA\Items(example={"product_acquisition_cost field is required."})),
     *
     *                  )
     *              ),
     *          ),
     *      ),
     * )
     */
    public function store(ProductRequest $request)
    {
        $product = Product::create($request->validated());
        $product->categories()->attach($request->validated('category_id'));

        return $this->sendSuccess(new ProductResource($product), 'Data berhasil disimpan.', 201);
    }

    /**
     * @OA\Get(
     *      path="/product/{product}",
     *      tags={"Product"},
     *      summary="Product details",
     *
     *      @OA\Parameter(in="path", required=true, name="product", @OA\Schema(type="integer"), description="product_id"),
     *
     *      @OA\Response(
     *          response=200,
     *          description="success",
     *      ),
     * )
     */
    public function show(Product $product)
    {
        return $this->sendSuccess(new ProductResource($product), 'Data berhasil ditampilkan.');
    }

    /**
     * @OA\Put(
     *      path="/product/{product}",
     *      tags={"Product"},
     *      summary="Update of Product",
     *
     *      @OA\Parameter(in="path", required=true, name="product", @OA\Schema(type="integer"), description="product_id"),
     *
     *      @OA\RequestBody(
     *         description="Body",
     *         required=true,
     *
     *         @OA\JsonContent(
     *
     *              @OA\Property(property="category_id", type="array", @OA\Items(type="integer")),
     *              @OA\Property(property="product_name", ref="#/components/schemas/Product/properties/product_name"),
     *              @OA\Property(property="product_desc", ref="#/components/schemas/Product/properties/product_desc"),
     *              @OA\Property(property="product_release_date", ref="#/components/schemas/Product/properties/product_release_date"),
     *              @OA\Property(property="product_acquisition_date", ref="#/components/schemas/Product/properties/product_acquisition_date"),
     *              @OA\Property(property="product_qty", ref="#/components/schemas/Product/properties/product_qty"),
     *              @OA\Property(property="product_acquisition_cost", ref="#/components/schemas/Product/properties/product_acquisition_cost"),
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
     *                      @OA\Property(property="product_name", type="array", @OA\Items(example={"product_name field is required."})),
     *                      @OA\Property(property="product_acquisition_date", type="array", @OA\Items(example={"product_acquisition_date field is required."})),
     *                      @OA\Property(property="product_qty", type="array", @OA\Items(example={"product_qty field is required."})),
     *                      @OA\Property(property="product_acquisition_cost", type="array", @OA\Items(example={"product_acquisition_cost field is required."})),
     *
     *                  )
     *              ),
     *          ),
     *      ),
     * )
     */
    public function update(ProductRequest $request, Product $product)
    {
        $product->update($request->validated());
        $product->categories()->sync($request->validated('category_id'));
        $product = $product->fresh();

        return $this->sendSuccess(new ProductResource($product), 'Data sukses disimpan.');
    }

    /**
     * @OA\Delete(
     *      path="/product/{product}",
     *      tags={"Product"},
     *      summary="Product Removal",
     *
     *      @OA\Parameter(in="path", required=true, name="product", @OA\Schema(type="integer"), description="product_id"),
     *
     *      @OA\Response(
     *          response=204,
     *          description="",
     *      ),
     * )
     */
    public function destroy(Product $product)
    {
        $product->categories()->detach();
        $product->delete();

        return $this->sendSuccess([], 'Data berhasil dihapus.', 204);
    }
}
