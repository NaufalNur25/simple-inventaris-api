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
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $rows = 10;
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
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        $category = Category::create($request->validated());

        return $this->sendSuccess(new CategoryResource($category), 'Data berhasil disimpan.', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return $this->sendSuccess(new CategoryResource($category), 'Data berhasil ditampilkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($request->validated());

        return $this->sendSuccess(new CategoryResource($category), 'Data sukses disimpan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return $this->sendSuccess([], null, 204);
    }
}
