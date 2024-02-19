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

                $slug = $userId . '/' . $post->id . '/' . strtotime(now()) . '-' . Str::random(5) . '-' . request()->name;

                // Get the bucket name and object key
                $bucket = env("AWS_BUCKET");
                $key = $slug;

                // Generate a pre-signed URL for the S3 object
                $cmd = $s3->getCommand('PutObject', [
                    'Bucket' => $bucket,
                    'Key' => $key
                ]);


                $presignedUrl = urldecode((string)$s3->createPresignedRequest($cmd, '+15 minutes')->getUri());
                $shortUrl = explode('?', $presignedUrl)[0];

                // when success save the data
                $media = Media::create([
                    "user_id" => $userId,
                    "type" => request()->type,
                    "name" => request()->name,
                    "size" => request()->size,
                    "mediable_id" => $post->id,
                    "mediable_class" => $post->getMorphClass(),
                    "url" => $shortUrl,
                ]);
                return $this->jsonSuccess(201, "Success", ['request_type' => "PUT", "url" => $presignedUrl]);
            } else {
                return $this->jsonError(403, 'Failed to find post');
            }

        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

}
