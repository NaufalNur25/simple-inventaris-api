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
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $rows = 10;
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
     * Store a newly created resource in storage.
     */
    public function store(ProductRequest $request)
    {
        $product = Product::create($request->validated());
        $product->categories()->attach($request->validated('category_id'));

        return $this->sendSuccess(new ProductResource($product), 'Data berhasil disimpan.', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        return $this->sendSuccess(new ProductResource($product), 'Data berhasil ditampilkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        $product->update($request->validated());
        $product->categories()->sync($request->validated('category_id'));
        $product = $product->fresh();

        return $this->sendSuccess(new ProductResource($product), 'Data sukses disimpan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->categories()->detach();
        $product->delete();

        return $this->sendSuccess([], 'Data berhasil dihapus.', 204);
    }
}
