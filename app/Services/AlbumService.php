<?php

namespace App\Services;

use Exception;
use App\Models\Album;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\NameResource;
use App\Http\Resources\AlbumResource;
use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;

class AlbumService extends Service {

    private $perPage;
	/**
    * AlbumService Constructor
    */
    public function __construct() {
        $this->perPage = request()->per_page ?? 10;
    }

    public function create(int $userId, array $data): JsonResponse
    {
        try{
            $album = Album::create([
                'user_id' => $userId,
                'name' => $data['name'],
                'status' => 'published'
            ]);
            return $this->jsonSuccess(201, 'Album created successfully!', ['album' => new NameResource($album)]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function get(int $userId, int $id): JsonResponse
    {
        try{
            $album = Album::where('id', $id)->where('user_id', $userId)->statusNot('deleted')->first();
            return $this->jsonSuccess(200, 'Success', ['album' => $album ? new AlbumResource($album) : []]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function list(int|null $userId = null): JsonResponse
    {
        try{
            $albums = Album::query();
            $albums->when($userId, function (Builder $query) use ($userId) {
                $query->where('user_id', $userId);
            })->whereLike('name', request()->name)
            ->statusNot('deleted');

            return $this->jsonSuccess(200, 'Success', ['albums' => AlbumResource::collection($albums->paginate($this->perPage))->resource]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function update(int $userId, int $id, array $data): JsonResponse
    {
        try{
            $isUpdated = Album::where('id', $id)->where('user_id', $userId)
                ->statusNot(['deleted', 'default'])
                ->update(['name' => $data['name']]);
            if( $isUpdated ) {
                return $this->jsonSuccess(200, 'Updated Successfully', ['album' => new NameResource(Album::find($id))]);
            } else {
                return $this->jsonError(403, 'No album found to udpate');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function delete(int $userId, int $id): JsonResponse
    {
        try{
            $is_deleted = Album::where('id', $id)->where('user_id', $userId)->statusNot(['default'])
                ->update(['status' => 'deleted']);
            if( $is_deleted ) {
                Post::where('album_id', $id)->update(['status' => 'deleted']);

                return $this->jsonSuccess(204, 'Album Deleted successfully');
            } else {
                return $this->jsonError(403, 'No album found to delete');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }
}
