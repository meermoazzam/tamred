<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\User\ActionRequest;
use App\Http\Requests\User\AttachCategoryRequest;
use App\Http\Requests\User\ProfilePictureRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Services\MediaService;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class UserController extends ApiController
{
    /**
	* @var userService
	* @var mediaService
	*/
	private $userService, $mediaService;

	/**
    * @param UserService
    * @param MediaService
    */
    public function __construct(UserService $userService, MediaService $mediaService) {
    	$this->userService = $userService;
    	$this->mediaService = $mediaService;
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
        return $this->userService->list(auth()->id());
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

    public function followerList($id): JsonResponse
    {
        return $this->userService->followerList($id);
    }

    public function followingList($id): JsonResponse
    {
        return $this->userService->followingList($id);
    }

    public function friendsList(): JsonResponse
    {
        return $this->userService->friendsList(auth()->id());
    }

    public function update(UpdateRequest $request): JsonResponse
    {
        return $this->userService->update(auth()->id(), $request->validated());
    }

    public function updateProfilePicture(ProfilePictureRequest $request): JsonResponse
    {
        return $this->userService->updateProfilePicture(auth()->id(), $request->validated());
    }
}
