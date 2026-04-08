<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\Support\SupportChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportChatController extends Controller
{
    public function __construct(private SupportChatService $chatService) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['status', 'hotel_id', 'customer_id']);
        $conversations = $this->chatService->getAllConversations($filters);

        return $this->success('Conversations retrieved.', $conversations);
    }

    public function assign(Request $request, int $conversationId): JsonResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $conversation = $this->chatService->assignConversation($conversationId, $request->user_id);

        return $this->success('Conversation assigned successfully.', $conversation);
    }
}
