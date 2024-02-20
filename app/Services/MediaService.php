<?php

namespace App\Services;

use Str;
use Exception;
use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\CommentResource;
use App\Models\Media;
use App\Models\Post;
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

                $slug = 'tamred/' . $userId . '/' . $postId . '/' . strtotime(now()) . '-' . Str::random(10) . '.' . $fileExtension;

                // Generate a pre-signed URL for the S3 object
                $cmd = $s3->getCommand('PutObject', [
                    'Bucket' => $bucket,
                    'Key' => $slug,
                    'ACL' => 'public-read',
                ]);

                $presignedUrl = urldecode((string)$s3->createPresignedRequest($cmd, '+20 minutes')->getUri());

                // when success save the data
                $createdMediaObject = Media::create([
                    "user_id" => $userId,
                    "type" => $media[$i]['type'],
                    "name" => $media[$i]['name'],
                    "size" => $media[$i]['size'],
                    "mediable_id" => $postId,
                    "mediable_type" => (new Post)->getMorphClass(),
                    "key" => $slug,
                    "sequence" => $i + 1,
                ]);

                $links[] = [
                    'name' => $createdMediaObject->name,
                    'sequence' => $createdMediaObject->name,
                    'type' => $createdMediaObject->type,
                    'url' => $presignedUrl,
                ];
            }
            return $links;
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }
}
