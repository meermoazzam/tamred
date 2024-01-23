<?php

namespace App\Http\Controllers\Api;

use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends ApiController
{
    /**
	* @var categoryService
	*/
	private $categoryService;

	/**
    * @param CategoryService
    */
    public function __construct(CategoryService $categoryService) {
    	$this->categoryService = $categoryService;
    }

    public function get($id): JsonResponse
    {
        return $this->categoryService->get($id);
    }

    public function list(): JsonResponse
    {
        return $this->categoryService->list();
    }
}
