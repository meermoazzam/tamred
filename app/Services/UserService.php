<?php

namespace App\Services;

use Exception;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\PersonalResource;
use App\Http\Resources\UserResource;
use App\Models\BlockUser;
use App\Models\FollowUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserService extends Service {

    private $perPage, $orderBy, $orderIn;
	/**
    * UserService Constructor
    */
    public function __construct() {
        $this->perPage = request()->per_page ?? 10;
        $this->orderBy = request()->order_by ?? 'id';
        $this->orderIn = request()->order_in ?? 'asc';
    }

    public function whoAmI(): JsonResponse
    {
        try{
            $user = User::with('categories')->find(auth()->id());
            return $this->jsonSuccess(200, 'Success', ['user' => new PersonalResource($user)]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function get(int $id): JsonResponse
    {
        try{
            $user = User::withCount(['post', 'follower', 'following'])->find($id);
            return $this->jsonSuccess(200, 'Success', ['user' => $user ? new UserResource($user) : []]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function list(): JsonResponse
    {
        try{
            $users = User::query();
            $users->when(request()->first_name, function (Builder $query) {
                $query->orWhereLike('first_name', request()->first_name);
            })->when(request()->last_name, function (Builder $query) {
                $query->orWhereLike('last_name', request()->last_name);
            })->when(request()->nickname, function (Builder $query) {
                $query->orWhereLike('nickname', request()->nickname);
            })
            ->withCount(['post', 'follower', 'following'])
            ->orderBy($this->orderBy, $this->orderIn);

            return $this->jsonSuccess(200, 'Success', ['users' => UserResource::collection($users->paginate($this->perPage))->resource]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function attachCategories(int $userId): JsonResponse
    {
        try{
            $user = User::where('id', $userId)->first();
            if( $user ) {
                $user->userCategories()->sync(request()->category_ids);
                return $this->jsonSuccess(200, 'Categories saved!');
            } else {
                return $this->jsonError(403, 'No user found to attach category');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function follow($follow_id): JsonResponse
    {
        try{
            $isBlocked = BlockUser::where(function ($query) use ($follow_id) {
                $query->where('user_id', $follow_id)->where('blocked_id', auth()->id());
            })->orWhere(function ($query) use ($follow_id) {
                $query->where('blocked_id', $follow_id)->where('user_id', auth()->id());
            })->first();

            if( !$isBlocked ) {
                $followUser = FollowUser::updateOrCreate([
                    'user_id' => auth()->id(),
                    'followed_id' => $follow_id,
                    'is_approved' => true,
                ],[]);
                return $this->jsonSuccess(200, 'Following!', []);
            } else {
                return $this->jsonError(403, "User can't be followed", []);
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function unfollow($unfollow_id): JsonResponse
    {
        try{
            $unFollowUser = FollowUser::where('user_id', auth()->id())
                ->where('followed_id', $unfollow_id)
                ->delete();

            return $this->jsonSuccess(200, 'Unfollowed!', []);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function block($block_id): JsonResponse
    {
        try{
            $blockUser = BlockUser::updateOrCreate([
                'user_id' => auth()->id(),
                'blocked_id' => $block_id,
            ],[]);

            // remove from the following/follower list as well
            $isCleared = FollowUser::where(function ($query) use ($block_id) {
                $query->where('user_id', $block_id)->where('followed_id', auth()->id());
            })->orWhere(function ($query) use ($block_id) {
                $query->where('followed_id', $block_id)->where('user_id', auth()->id());
            })->delete();

            return $this->jsonSuccess(200, 'Blocked!', []);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function unblock($unblock_id): JsonResponse
    {
        try{
            $unBlockUser = BlockUser::where('user_id', auth()->id())
                ->where('blocked_id', $unblock_id)
                ->delete();

            return $this->jsonSuccess(200, 'Unblocked!', []);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }
}
