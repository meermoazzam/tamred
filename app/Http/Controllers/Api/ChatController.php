<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\CreateConversationRequest;
use App\Http\Requests\Chat\GetMessageRequest;
use App\Http\Requests\Chat\GetParticipantRequest;
use App\Http\Requests\Chat\SendMessageRequest;
use App\Services\ChatService;
use Illuminate\Http\JsonResponse;

class ChatController extends Controller
{
    /**
	* @var chatService
	*/
	private $chatService;

	/**
    * @param ChatService
    */
    public function __construct(ChatService $chatService) {
    	$this->chatService = $chatService;
    }

    public function createConversation(CreateConversationRequest $request): JsonResponse
    {
        return $this->chatService->createConversation(auth()->id(), $request['user_id']);
    }

    public function getConversations(): JsonResponse
    {
        return $this->chatService->getConversations(auth()->id());
    }

    public function sendMessage(SendMessageRequest $request): JsonResponse
    {
        return $this->chatService->sendMessage(auth()->id(), $request);
    }

    public function getParticipants(GetParticipantRequest $request): JsonResponse
    {
        return $this->chatService->getParticipants(auth()->id());
    }

    public function getMessages(GetMessageRequest $request): JsonResponse
    {
        return $this->chatService->getMessages(auth()->id());
    }

    public function markAsRead(): JsonResponse
    {
        return $this->chatService->markAsRead(auth()->id());
    }

    public function deleteMessage($id): JsonResponse
    {
        return $this->chatService->deleteMessage(auth()->id(), $id);
    }
}
