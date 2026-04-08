<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Support\SendMessageRequest;
use App\Http\Requests\Api\Support\StartConversationRequest;
use App\Services\Support\SupportChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupportChatController extends Controller
{
    public function __construct(private SupportChatService $chatService) {}

    public function start(StartConversationRequest $request): JsonResponse
    {
        $conversation = $this->chatService->startConversation($request->validated());

        return $this->success('Conversation started successfully.', $conversation, 201);
    }

    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status');
        $conversations = $this->chatService->getCustomerConversations($status);

        return $this->success('Conversations retrieved.', $conversations);
    }

    public function show(int $id): JsonResponse
    {
        $conversation = $this->chatService->getCustomerConversation($id);

        return $this->success('Conversation retrieved.', $conversation);
    }

    public function sendMessage(SendMessageRequest $request, int $conversationId): JsonResponse
    {
        $message = $this->chatService->sendMessage($conversationId, $request->validated());

        return $this->success('Message sent successfully.', $message->load('sender:id,name'));
    }

    public function close(int $id): JsonResponse
    {
        $conversation = $this->chatService->closeConversation($id);

        return $this->success('Conversation closed.', $conversation);
    }
}
