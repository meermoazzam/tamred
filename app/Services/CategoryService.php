<?php

namespace App\Services;

use Exception;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\CategoryResource;
use Illuminate\Database\Eloquent\Builder;

class CategoryService extends Service {

    private $perPage, $orderBy, $orderIn;
	/**
    * CategoryService Constructor
    */
    public function __construct() {
        $this->perPage = request()->per_page ?? 10;
        $this->orderBy = request()->order_by ?? 'id';
        $this->orderIn = request()->order_in ?? 'asc';
    }

    public function get(int $id): JsonResponse
    {
        try{
            $category = Category::where('id', $id)
            ->with('subCategories')->first();
            return $this->jsonSuccess(200, 'Success', ['category' => $category ? new CategoryResource($category) : []]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function list(): JsonResponse
    {
        try{
            $categories = Category::query();
            $categories->whereLike('name', request()->name)
            ->with('subCategories')
            ->orderBy($this->orderBy, $this->orderIn);

            return $this->jsonSuccess(200, 'Success', ['categories' => CategoryResource::collection($categories->paginate($this->perPage))->resource]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }
}
