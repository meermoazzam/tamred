<?php

namespace App\Services;

use Exception;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\CategoryResource;
use App\Models\CategoryPost;
use App\Models\UserCategory;
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
            ->where('parent_id', null)
            ->with('subCategories')
            ->orderBy($this->orderBy, $this->orderIn);

            return $this->jsonSuccess(200, 'Success', ['categories' => CategoryResource::collection($categories->paginate($this->perPage))->resource]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function delete(int $id): JsonResponse
    {
        try{
            $isDeleted = Category::where('id', $id)->delete();
            $isUpdated = Category::where('parent_id', $id)->update(['parent_id' => null]);
            $delete_relations = CategoryPost::where('category_id', $id)->delete();
            $delete_relations = UserCategory::where('category_id', $id)->delete();

            if( $isDeleted ) {
                return $this->jsonSuccess(204, 'Category Deleted successfully');
            } else {
                return $this->jsonError(403, 'No Category found to delete');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }
}
