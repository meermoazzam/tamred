<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Media\GeneratePostUrl;
use App\Http\Requests\Media\DeleteRequest;
use App\Services\MediaService;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Mail\Mailables\Content;

class MediaController extends Controller
{
    /**
	* @var mediaService
	*/
	private $mediaService;

	/**
    * @param MediaService
    */
    public function __construct(MediaService $mediaService) {
    	$this->mediaService = $mediaService;
    }

    public function generatePostPresignedUrl(GeneratePostUrl $request)
    {
        return $this->mediaService->generatePostPresignedUrl(auth()->id(), $request);
    }

    public function deleteMedia(DeleteRequest $request)
    {
        return $this->mediaService->deleteMedia(auth()->id());
    }

    public function uploadFile(Request $request)
    {
        $file = $request->file;
        $url = $request->url;

        $client = new Client();

        $response = $client->request('PUT', $url, [
            'body' => $file,
            "header" => [
                "x-amz-acl" => "public-read",
                "Content-Type"=> "binary/octet-stream",
            ]
        ]);

        dd($response);
    }

}
