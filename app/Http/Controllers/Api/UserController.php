<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\User\ActionRequest;
use App\Http\Requests\User\AttachCategoryRequest;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends ApiController
{
    /**
	* @var userService
	*/
	private $userService;

	/**
    * @param UserService
    */
    public function __construct(UserService $userService) {
    	$this->userService = $userService;
    }

    public function whoAmI(): JsonResponse
    {
        return $this->userService->whoAmI();
    }

    public function get($id): JsonResponse
    {
        return $this->userService->get($id);
    }

    public function list(): JsonResponse
    {
        return $this->userService->list();
    }

    public function attachCategories(AttachCategoryRequest $request): JsonResponse
    {
        return $this->userService->attachCategories(auth()->id());
    }

    public function follow(ActionRequest $request): JsonResponse
    {
        return $this->userService->follow($request['user_id']);
    }

    public function unfollow(ActionRequest $request): JsonResponse
    {
        return $this->userService->unfollow($request['user_id']);
    }

    public function block(ActionRequest $request): JsonResponse
    {
        return $this->userService->block($request['user_id']);
    }

    public function unblock(ActionRequest $request): JsonResponse
    {
        return $this->userService->unblock($request['user_id']);
    }
}
