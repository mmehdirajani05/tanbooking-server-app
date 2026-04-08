<?php

namespace App\Http\Controllers\Api\HotelOwner;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Support\SendMessageRequest;
use App\Services\Support\SupportChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportChatController extends Controller
{
    public function __construct(private SupportChatService $chatService) {}

    public function index(int $hotelId, Request $request): JsonResponse
    {
        $status = $request->query('status');
        $conversations = $this->chatService->getHotelConversations($hotelId, $status);

        return $this->success('Conversations retrieved.', $conversations);
    }

    public function show(int $hotelId, int $id): JsonResponse
    {
        $conversation = $this->chatService->getHotelConversation($hotelId, $id);

        return $this->success('Conversation retrieved.', $conversation);
    }

    public function reply(SendMessageRequest $request, int $hotelId, int $conversationId): JsonResponse
    {
        $message = $this->chatService->replyAsHotelOwner($hotelId, $conversationId, $request->validated());

        return $this->success('Reply sent successfully.', $message->load('sender:id,name'));
    }
}
