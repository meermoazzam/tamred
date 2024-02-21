<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Media\GeneratePostUrl;
use App\Http\Requests\Media\DeleteRequest;
use App\Services\MediaService;
use Illuminate\Http\Request;

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

}
