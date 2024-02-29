<?php

namespace App\Services;

use Str;
use App\Http\Resources\Chat\ConversationResource;
use App\Http\Resources\Chat\MessageResource;
use App\Http\Resources\Chat\ParticipantResource;
use Exception;
use Illuminate\Http\JsonResponse;
use App\Models\Chat\Conversation;
use App\Models\Chat\Message;
use App\Models\Chat\Participant;
use App\Models\Media;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ChatService extends Service {

    private $perPage, $orderBy, $orderIn;
	/**
    * ChatService Constructor
    */
    public function __construct() {
        $this->perPage = request()->per_page ?? 10;
        $this->orderBy = request()->order_by ?? 'id';
        $this->orderIn = request()->order_in ?? 'asc';
    }

    public function createConversation(int $userId, int $participantId): JsonResponse
    {
        try{
            $participantIds = [$userId, $participantId];

            $conversation = Conversation::query();
            $conversation = $this->getConversationByParticipants($conversation, $participantIds)
                ->with('participants.user')->first();

            if( !$conversation ) {
                $conversation = Conversation::create([]);
                $participantsData = array_map(function ($user_id) {
                    return [
                        'user_id' => $user_id,
                        'seen_at' => now(),
                    ];
                }, $participantIds);
                $conversation->participants()->createMany($participantsData);
                $conversation->load('participants.user');
            }

            return $this->jsonSuccess(201, 'Success', ['conversation' => new ConversationResource($conversation)]);

        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function getConversations($userId): JsonResponse
    {
        try{
            $conversations = Conversation::query();
            $conversations->whereHas('participants', function (Builder $query) use ($userId) {
                $query->where($query->qualifyColumn('user_id'), $userId);
            })
            ->has('messages')
            ->with(['participants.user', 'latestMessage'])
            ->orderBy('updated_at', 'desc');


            $conversations = $conversations->paginate($this->perPage);

            // unseen message counts
            $updatedConversations = $conversations->getCollection()->map(function($conversation) use ($userId) {
                $participant = $conversation->participants->where('user_id', $userId)->first();
                $messageStatus = $participant->message_status;
                $seenAt = $participant->seen_at;
                if($messageStatus != 2) {
                    $messageCount = Message::where('conversation_id', $conversation->id)
                        ->statusNot(['deleted'])
                        ->whereNot('user_id', $userId)->where('created_at', '>', $seenAt)->count();
                } else {
                    $messageCount = 0;
                }

                $conversation->unseen_message_count = $messageCount;
                return $conversation;
            });

            $conversations->setCollection($updatedConversations);

            return $this->jsonSuccess(200, 'Success', ['conversations' => ConversationResource::collection($conversations)->resource]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function sendMessage(int $userId, Request $data): JsonResponse
    {
        try{
            $conversationCheck = Conversation::where('id', $data['conversation_id'])
                ->whereHas('participants', function(Builder $query) use ($userId) {
                    $query->where('user_id', $userId);
                })->exists();

            if($data['parent_id']) {
                $parentMessageCheck = Message::where('id', $data['parent_id'])->where('conversation_id', $data['conversation_id'])->exists();
            } else {
                $parentMessageCheck = true;
            }

            if($conversationCheck && $parentMessageCheck) {

                $message = Message::create([
                    'user_id' => $userId,
                    'conversation_id' => $data['conversation_id'],
                    'parent_id' => $data['parent_id'],
                    'description' => $data['description'],
                ]);

                if($message) {

                    $message->conversation()->update([]);
                    $message->conversation->participants()->whereNot('user_id', $userId)->update(['message_status' => 1]);

                    $content_slug = $thumb_slug = null;
                    if($data['file']) {
                        $content = $data['file'];
                        $content_slug = 'tamred/' . env('APP_ENV', 'dev') . '/chat/'. $data['conversation_id'] . '/' . $userId . '/' . $message->id . '-content-' . Str::random(10) . '.' . $content->getClientOriginalExtension();
                        Storage::disk(env('STORAGE_DISK', 's3'))->put($content_slug, file_get_contents($content));
                    }
                    if($data['thumbnail']) {
                        $thumb = $data['thumbnail'];
                        $thumb_slug = 'tamred/' . env('APP_ENV', 'dev') . '/chat/' . $data['conversation_id'] . '/' . $userId . '/' . $message->id . '-thumbnail-' . Str::random(10) . '.' . $thumb->getClientOriginalExtension();
                        Storage::disk(env('STORAGE_DISK', 's3'))->put($thumb_slug, file_get_contents($thumb));
                    }

                    if($content_slug || $thumb_slug) {
                        Media::create([
                            "user_id" => $userId,
                            "type" => $data['type'],
                            "size" => $data['size'],
                            "mediable_id" => $message->id,
                            "mediable_type" => $message->getMorphClass(),
                            "media_key" => $content_slug,
                            "thumbnail_key" => $thumb_slug,
                        ]);

                        $message->load('media');
                    }


                    return $this->jsonSuccess(200, 'Message sent successfully!', ['message' => new MessageResource($message)]);
                } else {
                    return $this->jsonError(500, 'Server Error: Failed to create message');
                }

            } else {
                return $this->jsonError(403, 'Conversation or parent comment not found');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function getParticipants($userId): JsonResponse
    {
        try{
            $participants = Participant::query();
            $participants->whereHas('conversation', function (Builder $query) {
                $query->where($query->qualifyColumn('id'), request()->conversation_id);
            })->whereHas('conversation.participants', function (Builder $query) use ($userId) {
                $query->where($query->qualifyColumn('user_id'), $userId);
            })->with('user')
            ->orderBy($this->orderBy, $this->orderIn);

            return $this->jsonSuccess(200, 'Success', ['conversations' => ParticipantResource::collection($participants->paginate($this->perPage))->resource]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function getMessages($userId): JsonResponse
    {
        try{
            $messages = Message::query();
            $messages->where('conversation_id', request()->conversation_id)
                ->whereHas('conversation.participants', function (Builder $query) use ($userId) {
                    $query->where($query->qualifyColumn('user_id'), $userId);
                })
                ->with('media');

            return $this->jsonSuccess(200, 'Success', ['messages' => MessageResource::collection($messages->paginate($this->perPage))->resource]);
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function deleteMessage(int $userId, int $id): JsonResponse
    {
        try{
            $isDeleted = Message::where('id', $id)->where('user_id', $userId)->update(['status' => 'deleted']);

            $isMediaDeleted = Media::where('user_id', $userId)
                ->where('mediable_id', $id)
                ->where('mediable_type', (new Message)->getMorphClass())
                ->update(['status' => 'deleted']);

            if( $isDeleted ) {
                return $this->jsonSuccess(204, 'Message deleted successfully');
            } else {
                return $this->jsonError(403, 'No message found to delete');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function markAsRead(int $userId): JsonResponse
    {
        try{
            $isRead = Participant::when(request()->conversation_id, function(Builder $query) {
                $query->whereHas('conversation', function (Builder $query) {
                    $query->where($query->qualifyColumn('id'), request()->conversation_id);
                });
            })->where('user_id', $userId)
            ->update([
                'message_status' => 2,
                'seen_at' => now(),
            ]);

            if( $isRead ) {
                return $this->jsonSuccess(200, 'Successfully marked as read!');
            } else {
                return $this->jsonError(403, 'Failed to mark as read');
            }
        } catch (Exception $e) {
            return $this->jsonException($e->getMessage());
        }
    }

    public function getConversationByParticipants(Builder $query, array $participantIds): Builder
    {
        return $query->whereHas('participants', function($query) use ($participantIds) {
                $query->whereIn('user_id', $participantIds);
        }, '=', count(array_unique($participantIds)));
    }
}
