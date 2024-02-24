<?php

namespace App\Services;

use Str;
use Exception;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\CommentResource;
use App\Models\Media;
use App\Models\Post;
use App\Models\User;
use Aws\AwsClient;
use Aws\Credentials\Credentials;
use Aws\S3\S3Client;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class MediaService extends Service {

    private $perPage, $orderBy, $orderIn;
	/**
    * MediaService Constructor
    */
    public function __construct() {
        $this->perPage = request()->per_page ?? 10;
        $this->orderBy = request()->order_by ?? 'id';
        $this->orderIn = request()->order_in ?? 'asc';
    }

    public function generatePostPresignedUrl(int $userId, Request $data): JsonResponse
    {
        try{
            $post = Post::where('id', $data['post_id'])->where('user_id', $userId)->statusNot('deleted')->first();

            if( $post ) {
                // generate url
                $s3 = new S3Client([
                    'region' => env('AWS_DEFAULT_REGION'),
                    'version' => 'latest',
                ]);

                $slug = 'tamred/' . $userId . '/' . $post->id . '/' . strtotime(now()) . '-' . Str::random(5) . '-' . request()->name;

                // Get the bucket name and object key
                $bucket = env("AWS_BUCKET");
                $key = $slug;

                // Generate a pre-signed URL for the S3 object
                $cmd = $s3->getCommand('PutObject', [
                    'Bucket' => $bucket,
                    'Key' => $key,
                    'ACL' => 'public-read',
                ]);


                $presignedUrl = urldecode((string)$s3->createPresignedRequest($cmd, '+15 minutes')->getUri());

                // when success save the data
                $media = Media::create([
                    "user_id" => $userId,
                    "type" => request()->type,
                    "name" => request()->name,
                    "size" => request()->size,
                    "mediable_id" => $post->id,
                    "mediable_type" => $post->getMorphClass(),
                    "key" => $slug,
                ]);
                return $this->jsonSuccess(201, "Success", ['request_type' => "PUT", "url" => $presignedUrl]);
            } else {
                return $this->jsonError(403, 'Failed to find post');
            }

        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function createBulkPresignedUrls(int $userId, int $postId, array|null $media) {
        $media = $media ?? [];
        try{
            $s3 = new S3Client([
                'region' => env('AWS_DEFAULT_REGION'),
                'version' => 'latest',
            ]);
            $bucket = env("AWS_BUCKET");

            $links = [];
            for ($i=0; $i < count($media); $i++) {

                $fileExtension = pathinfo($media[$i]['name'], PATHINFO_EXTENSION);

                $mediaSlug = 'tamred/media/' . $userId . '/' . $postId . '/' . strtotime(now()) . '-' . Str::random(10) . '.' . $fileExtension;
                $thumbnailSlug = 'tamred/thumbnails/' . $userId . '/' . $postId . '/' . strtotime(now()) . '-' . Str::random(10) . '.' . $fileExtension;

                // Generate a pre-signed URL for the S3 object
                $cmd = $s3->getCommand('PutObject', [
                    'Bucket' => $bucket,
                    'Key' => $mediaSlug,
                    'ACL' => 'public-read',
                ]);
                $presignedMediaUrl = urldecode((string)$s3->createPresignedRequest($cmd, '+20 minutes')->getUri());

                // Generate a pre-signed URL for the S3 object
                $cmd = $s3->getCommand('PutObject', [
                    'Bucket' => $bucket,
                    'Key' => $thumbnailSlug,
                    'ACL' => 'public-read',
                ]);
                $presignedThumbnailUrl = urldecode((string)$s3->createPresignedRequest($cmd, '+20 minutes')->getUri());

                // when success save the data
                $createdMediaObject = Media::create([
                    "user_id" => $userId,
                    "type" => $media[$i]['type'],
                    "name" => $media[$i]['name'],
                    "size" => $media[$i]['size'],
                    "mediable_id" => $postId,
                    "mediable_type" => (new Post)->getMorphClass(),
                    "media_key" => $mediaSlug,
                    "thumbnail_key" => $thumbnailSlug,
                    "sequence" => $i + 1,
                ]);

                $links[] = [
                    'name' => $createdMediaObject->name,
                    'sequence' => $createdMediaObject->name,
                    'type' => $createdMediaObject->type,
                    'mediaUrl' => $presignedMediaUrl,
                    'thumbnailUrl' => $presignedThumbnailUrl,
                ];
            }
            return $links;
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function deleteMedia(int $userId): JsonResponse
    {
        try{
            Media::where('user_id', $userId)->whereIn('id', request()->media_ids)->update(['status' => 'deleted']);
            return $this->jsonSuccess(204, 'Album Deleted successfully');
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function updateProfilePicture(int $userId, $media): JsonResponse
    {
        try{
            $s3 = new S3Client([
                'region' => env('AWS_DEFAULT_REGION'),
                'version' => 'latest',
            ]);
            $bucket = env("AWS_BUCKET");

            $fileExtension = pathinfo($media['name'], PATHINFO_EXTENSION);


            $mediaSlug = 'tamred/profile/pictures/' . $userId . '/' . strtotime(now()) . '-' . Str::random(10) . '.' . $fileExtension;
            $thumbnailSlug = 'tamred/profile/thumbnails/' . $userId . '/' . strtotime(now()) . '-' . Str::random(10) . '.' . $fileExtension;

            // Generate a pre-signed URL for the S3 object
            $cmd = $s3->getCommand('PutObject', [
                'Bucket' => $bucket,
                'Key' => $mediaSlug,
                'ACL' => 'public-read',
            ]);
            $presignedMediaUrl = urldecode((string)$s3->createPresignedRequest($cmd, '+20 minutes')->getUri());

            // Generate a pre-signed URL for the S3 object
            $cmd = $s3->getCommand('PutObject', [
                'Bucket' => $bucket,
                'Key' => $thumbnailSlug,
                'ACL' => 'public-read',
            ]);
            $presignedThumbnailUrl = urldecode((string)$s3->createPresignedRequest($cmd, '+20 minutes')->getUri());

            User::where('id', $userId)->update([
                'image' => $mediaSlug,
                'thumbnail' => $thumbnailSlug,
            ]);

            return $this->jsonSuccess(200, 'Updated Successfully', [
                'presignedMediaUrl' => $presignedMediaUrl,
                'presignedThumbnailUrl' => $presignedThumbnailUrl,
            ]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }
}
