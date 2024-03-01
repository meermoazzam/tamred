<?php

namespace App\Services;

use App\Http\Resources\ItineraryResource;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\NameResource;
use App\Http\Resources\PostResource;
use App\Models\Itinerary;
use App\Models\ItinPost;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ItinService extends Service {

    private $perPage, $orderBy, $orderIn;
	/**
    * AlbumService Constructor
    */
    public function __construct() {
        $this->perPage = request()->per_page ?? 10;
        $this->orderBy = request()->order_by ?? 'id';
        $this->orderIn = request()->order_in ?? 'asc';
    }

    public function create(int $userId, Request $data): JsonResponse
    {
        try{
            $itinerary = Itinerary::create([
                'user_id' => $userId,
                'name' => $data['name'],
                'data' => $data['data'],
                'status' => 'published'
            ]);

            $itinerary->posts()->attach($data['post_ids']);

            return $this->jsonSuccess(201, 'Itinerary created successfully!', ['itinerary' => new NameResource($itinerary)]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function get(int $userId, int $id): JsonResponse
    {
        try{
            $posts = Post::whereHas('itins', function (Builder $query) use ($userId, $id) {
                $query->where($query->qualifyColumn('id'), $id)
                    ->where($query->qualifyColumn('user_id'), $userId)
                    ->where($query->qualifyColumn('status'), 'published');
                })
                ->with('media')
                ->status('published')->get();

            return $this->jsonSuccess(200, 'Success', ['posts' => PostResource::collection($posts)]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function list(): JsonResponse
    {
        try{
            $itinerary = Itinerary::query();
            $itinerary->whereLike('name', request()->name)
            ->withCount('posts')
            ->with('user')
            ->orderBy($this->orderBy, $this->orderIn)
            ->statusNot('deleted');

            return $this->jsonSuccess(200, 'Success', ['itineraries' => ItineraryResource::collection($itinerary->paginate($this->perPage))->resource]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function update(int $userId, int $id, Request $data): JsonResponse
    {
        try{
            $itinerary = Itinerary::where('id', $id)->where('user_id', $userId)
                ->statusNot(['deleted'])->first();

            if($itinerary) {
                $isUpdated = Itinerary::where('id', $id)->update([
                    'name' => $data['name'],
                    'data' => $data['data'],
                ]);
                ItinPost::where("itin_id", $id)->delete();
                $itinerary->posts()->attach($data['post_ids']);
                return $this->jsonSuccess(200, 'Updated Successfully', ['itinerary' => new NameResource(Itinerary::find($id))]);
            } else {
                return $this->jsonError(403, 'No itinerary found to udpate');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function delete(int $userId, int $id): JsonResponse
    {
        try{
            $is_deleted = Itinerary::where('id', $id)->where('user_id', $userId)->update(['status' => 'deleted']);
            if( $is_deleted ) {
                return $this->jsonSuccess(204, 'Itinerary Deleted successfully');
            } else {
                return $this->jsonError(403, 'No Itinerary found to delete');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }
}
