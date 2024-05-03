<?php

namespace App\Services;

use DB;
use Str;
use Exception;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\PostResource;
use App\Http\Resources\ReactionResource;
use App\Http\Resources\SpecialPostResource;
use App\Http\Resources\UserShortResource;
use App\Models\Activities;
use App\Models\Add;
use App\Models\BlockUser;
use App\Models\Comment;
use App\Models\Media;
use App\Models\Reaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostService extends Service {


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

    public function create(int $userId, Request $data): JsonResponse
    {
        try{
            $post = Post::create([
                'user_id' => $userId,
                'title' => $data['title'],
                'description' => $data['description'],
                'status' => 'draft',
                'location' => $data['location'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'city' => $data['city'],
                'state' => $data['state'],
                'country' => $data['country'],
                'tags' => $data['tags'] ? array_unique($data['tags']) : [],
                'tagged_users' => $data['tagged_users'] ? array_unique($data['tagged_users']) : [],
                'allow_comments' => $data['allow_comments'] === false ? false : true,
            ]);

            // WRITE ACTIVITY
            foreach($post['tagged_users'] as $username) {
                $user = User::where('username', $username)->first();
                if($user) {
                    $this->activityService->generateActivity($user->id, $userId, 'tagged_on_post', $post->id);
                }
            }

            return $this->jsonSuccess(201, 'Post created successfully!',
                [
                    'post' => new PostResource($post),
                ]
            );
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function publish(int $userId, int $id): JsonResponse
    {
        try{
            $is_updated = Post::where('user_id', $userId)->where('id', $id)
            ->statusNot(['archived', 'deleted'])
            ->update([
                'status' => 'published',
            ]);

            if( $is_updated ) {
                return $this->jsonSuccess(200, 'Post published successfully!', []);
            } else {
                return $this->jsonError(403, 'Post publishing failed!', []);
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function get(int $userId, int $id): JsonResponse
    {
        try{
            $post = Post::where('id', $id)
                    ->with(['lastThreeLikes.user', 'user', 'media', 'categories',
                    'reactions' => function ($query) use ($userId) {
                        $query->where('user_id', $userId);
                    },'myAlbums' => function ($query) use ($userId) {
                        $query->where('user_id', $userId)->whereNot('status', 'deleted');
                    },
                ])->withCount('albums')
                ->status('published')->first();

            $taggedUsersData = $post?->tagged_users != null ? $post->tagged_users : [];
            $taggedUsersData = User::whereIn('username', array_unique($taggedUsersData))->get();

            return $this->jsonSuccess(200, 'Success', [
                'post' => $post ? new PostResource($post) : [],
                'tagged_users_data' => $taggedUsersData ? UserShortResource::collection($taggedUsersData) : []
            ]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function list($userId): JsonResponse
    {
        try{
            $blockedUserIds = BlockUser::where('blocked_id', $userId)
                ->orWhere('user_id', $userId)
                ->pluck('user_id', 'blocked_id')->toArray();
            $blockedUserIds = array_unique(array_merge(array_keys($blockedUserIds), array_values($blockedUserIds)));

            $posts = Post::query();
            $posts->when(request()->user_id, function (Builder $query) {
                $query->where('user_id', request()->user_id);
            })
            ->when(request()->title, function (Builder $query) {
                $query->orWhereLike('title', request()->title);
            })
            ->when(request()->description, function (Builder $query) {
                $query->orWhereLike('description', request()->description);
            })
            ->when(request()->city, function (Builder $query) {
                $query->orWhereLike('city', request()->city);
            })
            ->when(request()->state, function (Builder $query) {
                $query->orWhereLike('state', request()->state);
            })
            ->when(request()->country, function (Builder $query) {
                $query->orWhereLike('country', request()->country);
            })
            ->when(request()->tags, function (Builder $query) {
                $query->whereLike('tags', '"' . request()->tags . '"');
            })
            ->when(request()->album_id, function (Builder $query) use ($userId) {
                // specific case of getting data from saved posts (all favourite + current album_id)(or Gate)
                $query->whereHas('albumPosts.album', function (Builder $query) use ($userId) {
                    $query->whereIn($query->qualifyColumn('id'), [request()->album_id, request()->all_favourite_album_id])
                        ->where($query->qualifyColumn('status'), '!=', 'deleted');
                });
            })
            ->when(request()->categories, function (Builder $query) {
                $query->whereHas('categories', function (Builder $query) {
                    $query->whereIn($query->qualifyColumn('id'), request()->categories)
                        ->orWhereIn($query->qualifyColumn('parent_id'), request()->categories);
                });
            })
            ->whereHas('user', function (Builder $query) use ($userId, $blockedUserIds) {
                $query->where('status', 'active')
                ->whereNotIn('id', $blockedUserIds);
            })
            ->when(request()->not_my_following, function (Builder $query) use ($userId) {
                $query->whereDoesntHave('user.follower', function (Builder $query) use ($userId) {
                    $query->where('user_id', $userId);
                });
            })
            ->status('published')
            ->with(['lastThreeLikes.user', 'user', 'media', 'categories',
                'reactions' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                },'myAlbums' => function ($query) use ($userId) {
                    $query->where('user_id', $userId)->whereNot('status', 'deleted');
                },
            ])->withCount('albums')
            ->orderBy($this->orderBy, $this->orderIn);

            $posts = $posts->paginate($this->perPage);

            $taggedUsersData = $this->fetchTaggedUsers($posts);

            return $this->jsonSuccess(200, 'Success', [
                'posts' => PostResource::collection($posts)->resource,
                'tagged_users_data' => UserShortResource::collection($taggedUsersData->get())->resource
            ]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function listByAlbumId($userId): JsonResponse
    {
        try{
            $blockedUserIds = BlockUser::where('blocked_id', $userId)
                ->orWhere('user_id', $userId)
                ->pluck('user_id', 'blocked_id')->toArray();
            $blockedUserIds = array_unique(array_merge(array_keys($blockedUserIds), array_values($blockedUserIds)));

            $posts = Post::query();
            $posts->when(request()->user_id, function (Builder $query) {
                $query->where('user_id', request()->user_id);
            })
            ->when(request()->title, function (Builder $query) {
                $query->orWhereLike('title', request()->title);
            })
            ->when(request()->description, function (Builder $query) {
                $query->orWhereLike('description', request()->description);
            })
            ->when(request()->city, function (Builder $query) {
                $query->orWhereLike('city', request()->city);
            })
            ->when(request()->state, function (Builder $query) {
                $query->orWhereLike('state', request()->state);
            })
            ->when(request()->country, function (Builder $query) {
                $query->orWhereLike('country', request()->country);
            })
            ->when(request()->tags, function (Builder $query) {
                $query->whereLike('tags', '"' . request()->tags . '"');
            })
            ->when(request()->album_id, function (Builder $query) use ($userId) {
                $query->whereHas('albumPosts.album', function (Builder $query) use ($userId) {
                    $query->where($query->qualifyColumn('id'), request()->album_id)
                        ->where($query->qualifyColumn('status'), '!=', 'deleted');
                });
            })
            ->when(request()->categories, function (Builder $query) {
                $query->whereHas('categories', function (Builder $query) {
                    $query->whereIn($query->qualifyColumn('id'), request()->categories)
                        ->orWhereIn($query->qualifyColumn('parent_id'), request()->categories);
                });
            })
            ->whereHas('user', function (Builder $query) use ($userId, $blockedUserIds) {
                $query->where('status', 'active')
                    ->whereNotIn('id', $blockedUserIds);
            })
            ->status('published')
            ->with(['lastThreeLikes.user', 'user', 'media', 'categories',
                'reactions' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                },'myAlbums' => function ($query) use ($userId) {
                    $query->where('user_id', $userId)->whereNot('status', 'deleted');
                },
            ])->withCount('albums')
            ->orderBy($this->orderBy, $this->orderIn);

            $posts = $posts->paginate($this->perPage);

            if(request()->page == 1) {
                // Get the paginated items from the paginator
                $items = $posts->items();
                // Add the empty array at the start of the paginated items
                if(count($items)) {
                    array_unshift($items, $items[0]);
                }
                // Set the modified items back to the paginator
                $posts->setCollection(collect($items));
            }

            $taggedUsersData = $this->fetchTaggedUsers($posts);

            return $this->jsonSuccess(200, 'Success', [
                'posts' => SpecialPostResource::collection($posts)->resource,
                'tagged_users_data' => UserShortResource::collection($taggedUsersData->get())->resource
            ]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function listForHome($userId): JsonResponse
    {
        try{
            $user = User::find($userId);

            $blockedUserIds = BlockUser::where('blocked_id', $userId)
                ->orWhere('user_id', $userId)
                ->pluck('user_id', 'blocked_id')->toArray();
            $blockedUserIds = array_unique(array_merge(array_keys($blockedUserIds), array_values($blockedUserIds)));

            $postsByIFollow = Post::query();
            $postsByIFollow->whereHas('user', function (Builder $query) use ($userId, $blockedUserIds) {
                $query->where('status', 'active')
                    ->whereNotIn('id', $blockedUserIds)
                    ->whereNot('id', $userId);
            })->whereHas('user.follower', function (Builder $query) use ($userId) {
                $query->where('user_id', $userId);
            })->status('published')
            ->orderBy('created_at', 'desc');
            $postsByIFollowIds = $postsByIFollow->paginate(100)->pluck('id');

            // top 10 now
            $top10followedPeople = User::whereNotIn('id', $blockedUserIds)
                ->where('status', 'active')
                ->whereNot('id', $userId)
                ->withCount('follower')->orderByDesc('follower_count')
                ->take(10)->pluck('id')->toArray();

            $top10FollowedPosts = Post::query();
            $top10FollowedPosts->whereHas('user', function (Builder $query) {
                $query->where('status', 'active');
            })
            ->whereIn('user_id', $top10followedPeople)
            ->status('published')
            ->orderBy('created_at', 'desc');
            $top10FollowedPostIds = $top10FollowedPosts->paginate(100)->pluck('id');

            $finalIds = $postsByIFollowIds->merge($top10FollowedPostIds);
            $finalPosts = Post::whereIn('id', $finalIds)
                ->with(['lastThreeLikes.user', 'user', 'media', 'categories',
                    'reactions' => function ($query) use ($userId) {
                        $query->where('user_id', $userId);
                    },'myAlbums' => function ($query) use ($userId) {
                        $query->where('user_id', $userId)->whereNot('status', 'deleted');
                    },
                ])->withCount('albums')
                ->orderBy('created_at', 'desc')->get();

            $dateOfBirth = Carbon::parse($user->date_of_birth); // Replace '1990-05-15' with your date of birth
            $userAge = $dateOfBirth->age;

            $adds = Add::where(DB::raw('(6371 * acos(
                    cos(radians('.$user->latitude.'))
                    * cos(radians(latitude))
                    * cos(radians(longitude) - radians('.$user->longitude.'))
                    + sin(radians('.$user->latitude.'))
                    * sin(radians(latitude))
                ))'), '<=', DB::raw('`range`'))
                ->where('start_date', '<=', Carbon::today())
                ->where('end_date', '>=', Carbon::today())
                ->where('status', 'active')
                ->whereIn('gender', [$user->gender, 'all'])
                ->where('min_age', '<=', $userAge)
                ->where('max_age', '>=', $userAge)
                ->with('media')
                ->inRandomOrder()
                ->take((int)(count($finalPosts)/ 5))->get();


            $processedAdds = collect();
            foreach($adds as $add) {
                $add->id = (int)0;
                $add->user_id = (int)0;
                $add->is_add = true;
                $add->user = (object)[
                    "id" => 1,
                    "is_admin" => 0,
                    "first_name" => "",
                    "last_name" => "",
                    "bio" => "",
                    "nickname" => "",
                    "username" => "",
                    "email" => "",
                    "email_verified_at" => "2024-04-03T12:40:08.000000Z",
                    "date_of_birth" => "2024-03-07",
                    "gender" => "",
                    "location" => "",
                    "latitude" => "",
                    "longitude" => "",
                    "city" => "",
                    "state" => "",
                    "country" => "",
                    "language" => "",
                    "image" => "",
                    "thumbnail" => "",
                    "cover" => null,
                    "device_id" => null,
                    "notification_settings" => "",
                    "status" => "",
                    "created_at" => "2024-04-03T12:40:08.000000Z",
                    "updated_at" => "2024-04-03T12:40:08.000000Z"
                ];
                $add->my_reactions = [];
                $add->my_albums = [];
                $add->description = '';
                $add->location = '';
                $add->city = '';
                $add->state = '';
                $add->country = '';
                $add->tags = [];
                $add->tagged_users = [];
                $add->last_three_likes = [];
                $add->total_likes = (int)0;
                $add->albums_count = (int)0;
                $add->total_comments = (int)0;
                $add->allow_comments = (bool)0;
                $add->categories = [];
                $processedAdds->push($add);
            }

            $posts = collect();
            $taggedUsersData = [];
            $postCount = 0;
            $adCount = 0;

            foreach($finalPosts as $key => $post) {
                $postCount += 1;
                $taggedUsersData = array_merge($taggedUsersData, $post->tagged_users);
                $post->is_add = false;
                $post->link = '';
                $post->id = (int)$post->id;
                $post->user_id = (int)$post->user_id;
                $post->total_likes = (int)$post->total_likes;
                $post->albums_count = (int)$post->albums_count;
                $post->total_comments = (int)$post->total_comments;
                $post->allow_comments = (bool)$post->allow_comments;

                $posts->push($post);
                if($postCount % 5 == 0) {
                    if(isset($processedAdds[$adCount])) {
                        $posts->push($processedAdds[$adCount]);
                        $adCount++;
                    }
                }
            }

            $taggedUsersData = User::whereIn('username', array_unique($taggedUsersData));

            return $this->jsonSuccess(200, 'Success', [
                'posts' => $posts,
                'tagged_users_data' => UserShortResource::collection($taggedUsersData->get())->resource
            ]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function listByMostFollowedPeople($userId): JsonResponse
    {
        try{
            $blockedUserIds = BlockUser::where('blocked_id', $userId)
                ->orWhere('user_id', $userId)
                ->pluck('user_id', 'blocked_id')->toArray();
            $blockedUserIds = array_unique(array_merge(array_keys($blockedUserIds), array_values($blockedUserIds)));

            $top10followedPeople = User::whereNotIn('id', $blockedUserIds)
                ->where('status', 'active')
                ->whereNot('id', $userId)
                ->withCount('follower')->orderByDesc('follower_count')
                ->take(10)->pluck('id')->toArray();

            $posts = Post::whereHas('user', function (Builder $query) {
                $query->where('status', 'active');
            })
            ->whereIn('user_id', $top10followedPeople)
            ->when(request()->not_my_following, function (Builder $query) use ($userId) {
                $query->whereDoesntHave('user.follower', function (Builder $query) use ($userId) {
                    $query->where('user_id', $userId);
                });
            })
            ->status('published')
            ->with(['lastThreeLikes.user', 'user', 'media', 'categories',
                'reactions' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                },'myAlbums' => function ($query) use ($userId) {
                    $query->where('user_id', $userId)->whereNot('status', 'deleted');
                },
            ])->withCount('albums')
            ->orderBy($this->orderBy, $this->orderIn)
            ->take(100)->get();

            $taggedUsersData = $this->fetchTaggedUsers($posts);

            return $this->jsonSuccess(200, 'Success', [
                'posts' => PostResource::collection($posts)->resource,
                'tagged_users_data' => UserShortResource::collection($taggedUsersData->get())->resource
            ]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function listByNearMe($userId): JsonResponse
    {
        try{
            $blockedUserIds = BlockUser::where('blocked_id', $userId)
                ->orWhere('user_id', $userId)
                ->pluck('user_id', 'blocked_id')->toArray();
            $blockedUserIds = array_unique(array_merge(array_keys($blockedUserIds), array_values($blockedUserIds)));

            $user = User::find($userId);
            $userLatitude = request()->latitude ?? $user->latitude;
            $userLongitude = request()->longitude ?? $user->longitude;
            // Radius in kilometers
            $radius = request()->radius ?? 100;
            $maxLat = $userLatitude + rad2deg($radius / 6371);
            $minLat = $userLatitude - rad2deg($radius / 6371);
            $maxLon = $userLongitude + rad2deg(asin($radius / 6371) / cos(deg2rad($userLatitude)));
            $minLon = $userLongitude - rad2deg(asin($radius / 6371) / cos(deg2rad($userLatitude)));

            $posts = Post::query();
            $posts->whereHas('user', function (Builder $query) {
                $query->where('status', 'active');
            })
            ->whereBetween('latitude', [$minLat, $maxLat])
            ->whereBetween('longitude', [$minLon, $maxLon])
            ->whereNotIn('user_id', $blockedUserIds)
            ->status('published')
            ->when(request()->categories, function (Builder $query) {
                $query->whereHas('categories', function (Builder $query) {
                    $query->whereIn($query->qualifyColumn('id'), request()->categories)
                        ->orWhereIn($query->qualifyColumn('parent_id'), request()->categories);
                });
            })
            ->with(['lastThreeLikes.user', 'user', 'media', 'categories',
                'reactions' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                },'myAlbums' => function ($query) use ($userId) {
                    $query->where('user_id', $userId)->whereNot('status', 'deleted');
                },
            ])->withCount('albums')
            ->orderBy($this->orderBy, $this->orderIn);

            $posts = $posts->paginate($this->perPage);

            $taggedUsersData = $this->fetchTaggedUsers($posts);

            return $this->jsonSuccess(200, 'Success', [
                'posts' => PostResource::collection($posts)->resource,
                'tagged_users_data' => UserShortResource::collection($taggedUsersData->get())->resource
            ]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function listByUsersIFollow($userId): JsonResponse
    {
        try{
            $blockedUserIds = BlockUser::where('blocked_id', $userId)
                ->orWhere('user_id', $userId)
                ->pluck('user_id', 'blocked_id')->toArray();
            $blockedUserIds = array_unique(array_merge(array_keys($blockedUserIds), array_values($blockedUserIds)));

            $posts = Post::query();
            $posts->when(request()->title, function (Builder $query) {
                $query->whereLike('title', request()->title);
            })
            ->when(request()->description, function (Builder $query) {
                $query->whereLike('description', request()->description);
            })
            ->when(request()->city, function (Builder $query) {
                $query->whereLike('city', request()->city);
            })
            ->when(request()->state, function (Builder $query) {
                $query->whereLike('state', request()->state);
            })
            ->when(request()->country, function (Builder $query) {
                $query->whereLike('country', request()->country);
            })
            ->when(request()->tags, function (Builder $query) {
                $query->whereLike('tags', '"' . request()->tags . '"');
            })
            ->when(request()->categories, function (Builder $query) {
                $query->whereHas('categories', function (Builder $query) {
                    $query->whereIn($query->qualifyColumn('id'), request()->categories)
                        ->orWhereIn($query->qualifyColumn('parent_id'), request()->categories);
                });
            })
            ->whereHas('user', function (Builder $query) use ($userId, $blockedUserIds) {
                $query->where('status', 'active')
                    ->whereNot('id', $userId)
                    ->whereNotIn('id', $blockedUserIds);
            })->whereHas('user.follower', function (Builder $query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->status('published')
            ->with(['lastThreeLikes.user', 'user', 'media', 'categories',
                'reactions' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                },'myAlbums' => function ($query) use ($userId) {
                    $query->where('user_id', $userId)->whereNot('status', 'deleted');
                },
            ])->withCount('albums')
            ->orderBy($this->orderBy, $this->orderIn);

            $posts = $posts->paginate($this->perPage);

            $taggedUsersData = $this->fetchTaggedUsers($posts);

            return $this->jsonSuccess(200, 'Success', [
                'posts' => PostResource::collection($posts)->resource,
                'tagged_users_data' => UserShortResource::collection($taggedUsersData->get())->resource
            ]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function listByMyFriends($userId): JsonResponse
    {
        try{
            $blockedUserIds = BlockUser::where('blocked_id', $userId)
                ->orWhere('user_id', $userId)
                ->pluck('user_id', 'blocked_id')->toArray();
            $blockedUserIds = array_unique(array_merge(array_keys($blockedUserIds), array_values($blockedUserIds)));

            $posts = Post::query();
            $posts->when(request()->title, function (Builder $query) {
                $query->whereLike('title', request()->title);
            })
            ->when(request()->description, function (Builder $query) {
                $query->whereLike('description', request()->description);
            })
            ->when(request()->city, function (Builder $query) {
                $query->whereLike('city', request()->city);
            })
            ->when(request()->state, function (Builder $query) {
                $query->whereLike('state', request()->state);
            })
            ->when(request()->country, function (Builder $query) {
                $query->whereLike('country', request()->country);
            })
            ->when(request()->tags, function (Builder $query) {
                $query->whereLike('tags', '"' . request()->tags . '"');
            })
            ->when(request()->categories, function (Builder $query) {
                $query->whereHas('categories', function (Builder $query) {
                    $query->whereIn($query->qualifyColumn('id'), request()->categories)
                        ->orWhereIn($query->qualifyColumn('parent_id'), request()->categories);
                });
            })
            ->whereHas('user', function (Builder $query) use ($userId, $blockedUserIds) {
                $query->where('status', 'active')
                    ->whereNot('id', $userId)
                    ->whereNotIn('id', $blockedUserIds);
            })
            ->whereHas('user.follower', function (Builder $query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->whereHas('user.following', function (Builder $query) use ($userId) {
                $query->where('followed_id', $userId);
            })
            ->status('published')
            ->with(['lastThreeLikes.user', 'user', 'media', 'categories',
                'reactions' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                },'myAlbums' => function ($query) use ($userId) {
                    $query->where('user_id', $userId)->whereNot('status', 'deleted');
                },
            ])->withCount('albums')
            ->orderBy($this->orderBy, $this->orderIn);

            $posts = $posts->paginate($this->perPage);

            $taggedUsersData = $this->fetchTaggedUsers($posts);

            return $this->jsonSuccess(200, 'Success', [
                'posts' => PostResource::collection($posts)->resource,
                'tagged_users_data' => UserShortResource::collection($taggedUsersData->get())->resource
            ]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function listByRandom($userId): JsonResponse
    {
        try{
            $blockedUserIds = BlockUser::where('blocked_id', $userId)
                ->orWhere('user_id', $userId)
                ->pluck('user_id', 'blocked_id')->toArray();
            $blockedUserIds = array_unique(array_merge(array_keys($blockedUserIds), array_values($blockedUserIds)));

            $posts = Post::when(request()->city, function (Builder $query) {
                $query->whereLike('city', request()->city);
            })
            ->when(request()->state, function (Builder $query) {
                $query->whereLike('state', request()->state);
            })
            ->when(request()->country, function (Builder $query) {
                $query->whereLike('country', request()->country);
            })
            ->when(request()->tags, function (Builder $query) {
                $query->whereLike('tags', '"' . request()->tags . '"');
            })
            ->when(request()->categories, function (Builder $query) {
                $query->whereHas('categories', function (Builder $query) {
                    $query->whereIn($query->qualifyColumn('id'), request()->categories)
                        ->orWhereIn($query->qualifyColumn('parent_id'), request()->categories);
                });
            })
            ->whereHas('user', function (Builder $query) use ($userId, $blockedUserIds) {
                $query->where('status', 'active')
                    ->whereNot('id', $userId)
                    ->whereNotIn('id', $blockedUserIds);
            })
            ->status('published')
            ->with(['lastThreeLikes.user', 'user', 'media', 'categories',
                'reactions' => function ($query) use ($userId) {
                    $query->where('user_id', $userId);
                },'myAlbums' => function ($query) use ($userId) {
                    $query->where('user_id', $userId)->whereNot('status', 'deleted');
                },
            ])->withCount('albums')
            ->inRandomOrder()->take(10)->get();

            $taggedUsersData = $this->fetchTaggedUsers($posts);

            return $this->jsonSuccess(200, 'Success', [
                'posts' => PostResource::collection($posts)->resource,
                'tagged_users_data' => UserShortResource::collection($taggedUsersData->get())->resource
            ]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function fetchTaggedUsers($posts) {
        $taggedUsersData = [];
        foreach($posts as $post) {
            $taggedUsersData = array_merge($taggedUsersData, $post->tagged_users);
        }
        $taggedUsersData = User::whereIn('username', array_unique($taggedUsersData));
        return $taggedUsersData;
    }

    public function update(int $userId, int $id, array $data): JsonResponse
    {
        try{
            $isUpdated = Post::where('id', $id)->where('user_id', $userId)
                ->statusNot(['archived', 'deleted'])
                ->update($data);

            if( $isUpdated ) {

                // WRITE ACTIVITY
                foreach($data['tagged_users'] as $username) {
                    $user = User::where('username', $username)->first();
                    if($user) {
                        $activity = Activities::where('user_id', $user->id)
                            ->where('caused_by', $userId)
                            ->where('model_id', $id)
                            ->where('type', 'tagged_on_post')
                            ->first();

                        if( !$activity ) {
                            $this->activityService->generateActivity($user->id, $userId, 'tagged_on_post', $id);
                        }
                    }
                }

                return $this->jsonSuccess(200, 'Updated Successfully', ['post' => new PostResource(Post::find($id))]);
            } else {
                return $this->jsonError(403, 'No post found to udpate');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function delete(int $userId, int $id): JsonResponse
    {
        try{
            $is_deleted = Post::where('id', $id)->where('user_id', $userId)
                ->update(['status' => 'deleted']);

            $isMediaDeleted = Media::where('user_id', $userId)
                ->where('mediable_id', $id)
                ->where('mediable_type', (new Post)->getMorphClass())
                ->update(['status' => 'deleted']);

            $isCommentsDeleted = Comment::where('post_id', $id)
                ->update(['status' => 'deleted']);

            if( $is_deleted ) {
                return $this->jsonSuccess(204, 'Post Deleted successfully');
            } else {
                return $this->jsonError(403, 'No post found to delete');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function attachCategories(int $userId, int $id): JsonResponse
    {
        try{
            $post = Post::where('id', $id)->where('user_id', $userId)
                ->statusNot(['archived', 'deleted'])->first();
            if( $post ) {
                $post->categories()->sync(request()->category_ids);
                return $this->jsonSuccess(200, 'Categories attached!');
            } else {
                return $this->jsonError(403, 'No post found to attach category');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function react(int $userId, int $id): JsonResponse
    {
        try{
            // check if post exist or available to react
            $post = Post::where('id', $id)->status(['published'])->first();

            if($post) {
                if(true == request()->react) {
                    // create the reaction
                    $reaction = Reaction::firstOrCreate([
                        'user_id' => $userId,
                        'type' => request()->type,
                        'reactable_id' => $id,
                        'reactable_class' => $post->getMorphClass(),
                    ],[]);

                    // update the total likes column in posts
                    if($reaction->wasRecentlyCreated) $post->increment('total_likes');

                    // WRITE ACTIVITY
                    $this->activityService->generateActivity($post->user_id, $userId, 'liked', $post->id);

                } else {
                    // remove the reaction
                    $is_deleted = Reaction::where('user_id', $userId)
                        ->where('type', request()->type)
                        ->where('reactable_id', $id)
                        ->where('reactable_class', $post->getMorphClass())
                        ->delete();

                    // update the total likes column in posts
                    if($is_deleted) $post->decrement('total_likes');
                }
                return $this->jsonSuccess(200, 'Reaction updated!');
            } else {
                return $this->jsonError(403, 'Post not found');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function reactList($id): JsonResponse
    {
        try{
            // check if post exist or available to react
            $post = Post::where('id', $id)->status(['published'])->first();

            if($post) {
                $reactions = Reaction::query();
                $reactions->when(request()->type, function (Builder $query) {
                    $query->where('type', request()->type);
                })
                ->where('reactable_id', $id)
                ->where('reactable_class', $post->getMorphClass())
                ->with('user')
                ->orderBy($this->orderBy, $this->orderIn);

                return $this->jsonSuccess(200, 'Success', ['reactions' => ReactionResource::collection($reactions->paginate($this->perPage))->resource]);
            } else {
                return $this->jsonError(403, 'Post not found');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function uploadMedia($userId, $request): JsonResponse
    {
        try{
            // check if post exist or available to react
            $post = Post::where('id', $request['post_id'])->where('user_id', $userId)
                ->statusNot(['deleted'])->first();

            if($post) {
                $content = $request['file'];
                $thumb = $request['thumbnail'];

                $content_slug = 'tamred/' . env('APP_ENV', 'dev') . '/media/users/' . $userId . '/post-' . $post->id . '-content-' . Str::random(10) . '.' . $content->getClientOriginalExtension();
                $thumb_slug = 'tamred/' . env('APP_ENV', 'dev') . '/media/users/' . $userId . '/post-' . $post->id . '-thumbnail-' . Str::random(10) . '.' . $thumb->getClientOriginalExtension();

                // Upload the file to S3
                Storage::disk(env('STORAGE_DISK', 's3'))->put($thumb_slug, file_get_contents($thumb));
                Storage::disk(env('STORAGE_DISK', 's3'))->put($content_slug, file_get_contents($content));

                $data = [
                    "user_id" => $userId,
                    "type" => $request['type'],
                    "size" => $request['size'],
                    "mediable_id" => $post->id,
                    "mediable_type" => $post->getMorphClass(),
                    "media_key" => $content_slug,
                    "thumbnail_key" => $thumb_slug,
                ];

                Media::create($data);

                return $this->jsonSuccess(200, 'Media Uploaded Successfully!');
            } else {
                return $this->jsonError(403, 'Post not found or deleted');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function deleteMedia($userId, $request): JsonResponse
    {
        try{
            Media::where('user_id', $userId)
                ->whereIn('id', $request['media_ids'])
                ->update(['status' => 'deleted']);

            return $this->jsonSuccess(204, 'Media Deleted Successfully!');
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }
}
