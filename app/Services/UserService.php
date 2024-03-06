<?php

namespace App\Services;

use Str;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\FollowResource;
use App\Http\Resources\PersonalResource;
use App\Http\Resources\UserResource;
use App\Mail\DeleteAccount;
use App\Models\Album;
use App\Models\BlockUser;
use App\Models\Comment;
use App\Models\FollowUser;
use App\Models\Post;
use App\Models\User;
use App\Models\UserMeta;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class UserService extends Service
{

    private $perPage, $orderBy, $orderIn;
    /**
	* @var activityService
	*/
	private $activityService;

	/**
     * PostService Constructor
     * @param ActivityService
    */
    public function __construct(ActivityService $activityService) {
        $this->perPage = request()->per_page ?? 10;
        $this->orderBy = request()->order_by ?? 'id';
        $this->orderIn = request()->order_in ?? 'asc';
        $this->activityService = $activityService;
    }

    public function whoAmI(): JsonResponse
    {
        try {
            $user = User::with('categories')->withCount('post')->find(auth()->id());
            return $this->jsonSuccess(200, 'Success', ['user' => new PersonalResource($user)]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function get(int $id): JsonResponse
    {
        try {
            $user = User::withCount(['post', 'follower', 'following'])->find($id);

            $user->inMyFollowing = FollowUser::where('user_id', auth()->id())->where('followed_id', $id)->exists();
            $user->isMyFollower = FollowUser::where('user_id', $id)->where('followed_id', auth()->id())->exists();

            return $this->jsonSuccess(200, 'Success', ['user' => $user ? new UserResource($user) : []]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function list($userId): JsonResponse
    {
        try {
            $blockedUserIds = BlockUser::where('blocked_id', $userId)
                ->orWhere('user_id', $userId)
                ->pluck('user_id', 'blocked_id')->toArray();
            $blockedUserIds = array_unique(array_merge(array_keys($blockedUserIds), array_values($blockedUserIds)));

            $users = User::query();
            $users->when(request()->first_name, function (Builder $query) {
                $query->orWhereLike('first_name', request()->first_name);
            })->when(request()->last_name, function (Builder $query) {
                $query->orWhereLike('last_name', request()->last_name);
            })->when(request()->nickname, function (Builder $query) {
                $query->orWhereLike('nickname', request()->nickname);
            })->when(request()->username, function (Builder $query) {
                $query->orWhereLike('username', request()->username);
            })
                ->whereNot('id', $userId)
                ->whereNotIn('id', $blockedUserIds)
                ->withCount(['post', 'follower', 'following'])
                ->orderBy($this->orderBy, $this->orderIn);

            $users = $users->paginate($this->perPage);

            $updatedUsers = $users->getCollection()->map(function ($user) {
                $user->inMyFollowing = FollowUser::where('user_id', auth()->id())->where('followed_id', $user->id)->exists();
                $user->isMyFollower = FollowUser::where('user_id', $user->id)->where('followed_id', auth()->id())->exists();
                return $user;
            });

            $users->setCollection($updatedUsers);

            return $this->jsonSuccess(200, 'Success', ['users' => UserResource::collection($users)->resource]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function listBySuggestion($userId): JsonResponse
    {
        try {
            $blockedUserIds = BlockUser::where('blocked_id', $userId)
                ->orWhere('user_id', $userId)
                ->pluck('user_id', 'blocked_id')->toArray();
            $blockedUserIds = array_unique(array_merge(array_keys($blockedUserIds), array_values($blockedUserIds)));

            $user = User::find($userId);
            $userLatitude = request()->latitude ?? $user->latitude;
            $userLongitude = request()->longitude ?? $user->longitude;

            $users = User::query();
            $users->select('*')
                ->selectRaw(
                    '(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance',
                    [$userLatitude, $userLongitude, $userLatitude]
                )
                ->orderByRaw('distance')
                ->whereDoesntHave('follower', function (Builder $query) use ($userId) {
                    $query->where('user_id', $userId);
                })
                ->whereNot('id', $userId)
                ->whereNotIn('id', $blockedUserIds)
                ->withCount(['post', 'follower', 'following']);

            $users = $users->paginate($this->perPage);

            $updatedUsers = $users->getCollection()->map(function ($user) {
                $user->inMyFollowing = FollowUser::where('user_id', auth()->id())->where('followed_id', $user->id)->exists();
                $user->isMyFollower = FollowUser::where('user_id', $user->id)->where('followed_id', auth()->id())->exists();
                return $user;
            });

            $users->setCollection($updatedUsers);

            return $this->jsonSuccess(200, 'Success', ['users' => UserResource::collection($users)->resource]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function attachCategories(int $userId): JsonResponse
    {
        try {
            $user = User::where('id', $userId)->first();
            if ($user) {
                $user->categories()->sync(request()->category_ids);
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
        try {
            $isBlocked = BlockUser::where(function ($query) use ($follow_id) {
                $query->where('user_id', $follow_id)->where('blocked_id', auth()->id());
            })->orWhere(function ($query) use ($follow_id) {
                $query->where('blocked_id', $follow_id)->where('user_id', auth()->id());
            })->first();

            if (!$isBlocked) {
                $followUser = FollowUser::updateOrCreate([
                    'user_id' => auth()->id(),
                    'followed_id' => $follow_id,
                    'is_approved' => true,
                ], []);

                // WRITE ACTIVITY
                $this->activityService->generateActivity($follow_id, auth()->id(), 'followed');

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
        try {
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
        try {
            $blockUser = BlockUser::updateOrCreate([
                'user_id' => auth()->id(),
                'blocked_id' => $block_id,
            ], []);

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
        try {
            $unBlockUser = BlockUser::where('user_id', auth()->id())
                ->where('blocked_id', $unblock_id)
                ->delete();

            return $this->jsonSuccess(200, 'Unblocked!', []);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function followerList($userId): JsonResponse
    {
        try {
            $followers = FollowUser::query();
            $followers->whereHas('userDetailByUserId')
                ->whereHas('userDetailByFollowedId')    
                ->where('followed_id', $userId)
                ->with('userDetailByUserId');

            $followers = $followers->paginate($this->perPage);

            $updatedFollowers = $followers->getCollection()->map(function ($follower) {
                $follower->inMyFollowing = FollowUser::where('user_id', auth()->id())->where('followed_id', $follower->user_id)->exists();
                $follower->isMyFollower = FollowUser::where('user_id', $follower->user_id)->where('followed_id', auth()->id())->exists();
                return $follower;
            });

            $followers->setCollection($updatedFollowers);

            return $this->jsonSuccess(200, 'Success', ['followers' => FollowResource::collection($followers)->resource]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function followingList($userId): JsonResponse
    {
        try {
            $followings = FollowUser::query();
            $followings->whereHas('userDetailByUserId')
                ->whereHas('userDetailByFollowedId')
                ->where('user_id', $userId)
                ->with('userDetailByFollowedId');

            $followings = $followings->paginate($this->perPage);

            $updatedFollowers = $followings->getCollection()->map(function ($following) {
                $following->inMyFollowing = FollowUser::where('user_id', auth()->id())->where('followed_id', $following->followed_id)->exists();
                $following->isMyFollower = FollowUser::where('user_id', $following->followed_id)->where('followed_id', auth()->id())->exists();
                return $following;
            });

            $followings->setCollection($updatedFollowers);

            return $this->jsonSuccess(200, 'Success', ['followings' => FollowResource::collection($followings)->resource]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function friendsList($userId): JsonResponse
    {
        try {
            $friends = User::query();
            $friends->whereHas('follower', function (Builder $query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->whereHas('following', function (Builder $query) use ($userId) {
                $query->where('followed_id', $userId);
            })
            ->withCount('post', 'follower', 'following')
            ->orderBy($this->orderBy, $this->orderIn);

            return $this->jsonSuccess(200, 'Success', ['friends' => UserResource::collection($friends->paginate($this->perPage))->resource]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function update(int $userId, array $data): JsonResponse
    {
        try {
            $isUpdated = User::where('id', $userId)
                ->update($data);

            return $this->jsonSuccess(200, 'Updated Successfully', ['user' => new PersonalResource(User::find($userId))]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function updateProfilePicture(int $userId, $request): JsonResponse
    {
        try {
            $content = $request['file'];
            $thumb = $request['thumbnail'];

            $content_slug = 'tamred/' . env('APP_ENV', 'dev') . '/profile-pictures/users/' . $userId . '/profile-image-' . strtotime(now()) . '-' . Str::random(10) . '.' . $content->getClientOriginalExtension();
            $thumb_slug = 'tamred/' . env('APP_ENV', 'dev') . '/profile-pictures/users/' . $userId . '/profile-thumbnail-' . strtotime(now()) . '-' . Str::random(10) . '.' . $thumb->getClientOriginalExtension();

            // Upload the file to S3
            Storage::disk(env('STORAGE_DISK', 's3'))->put($thumb_slug, file_get_contents($thumb));
            Storage::disk(env('STORAGE_DISK', 's3'))->put($content_slug, file_get_contents($content));

            $data = [
                "image" => $content_slug,
                "thumbnail" => $thumb_slug,
            ];

            User::where('id', $userId)->update($data);

            return $this->jsonSuccess(200, 'Profile picture uploaded successfully!');
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function updateDeviceId(int $userId, Request $request): JsonResponse
    {
        try {
            User::where('id', $userId)->update(['device_id' => $request->device_id]);
            return $this->jsonSuccess(200, 'Device Id updated successfully!');
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function deleteRequest(int $userId, Request $request): JsonResponse
    {
        try {
            $user = User::find($userId);
            $token = Str::random(128);
            UserMeta::updateOrCreate([
                'user_id' => $userId,
                'meta_key' => 'account_delete_token',
            ],[
                'meta_value' => $token,
            ]);

            $message = 'Please use this link to delete you account, Open this url in your browser, URL: ' . route('user.account.delete', ['token' => $token]);
            $emailStatus = Mail::to($user)->send(new DeleteAccount($user->name, $message));
            // generate email
            if($emailStatus) {
                return $this->jsonSuccess(200, 'An Email has bee sent to your email address, please use that link to delete your account.');
            } else {
                return $this->jsonError(400, "Failed to send account deletion email, Please contact support.");
            }

        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function deleteAction($request) {
        try {
            $token = $request->token;
            $meta = UserMeta::where('meta_key', 'account_delete_token')->where('meta_value', $token)->first();
            if($meta && $token != null) {
                UserMeta::where('id', $meta->id)->delete();
                User::where('id', $meta->user_id)->update(['status' => 'deleted']);
                Post::where('user_id', $meta->user_id)->update(['status' => 'deleted']);
                Album::where('user_id', $meta->user_id)->update(['status' => 'deleted']);
                Comment::where('user_id', $meta->user_id)->update(['status' => 'deleted']);
                return view('thankyou');
            }
            return view('oops');
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }
}
