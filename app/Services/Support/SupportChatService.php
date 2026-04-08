<?php

namespace App\Services\Support;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class SupportChatService
{
    public function startConversation(array $data): Conversation
    {
        $conversation = Conversation::create([
            'customer_id' => Auth::id(),
            'hotel_id'    => $data['hotel_id'] ?? null,
            'subject'     => $data['subject'] ?? null,
            'status'      => 'open',
        ]);

        // Create the first message
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => Auth::id(),
            'message'         => $data['message'],
        ]);

        return Conversation::with(['hotel:id,name', 'messages.sender:id,name'])
            ->findOrFail($conversation->id);
    }

    public function getCustomerConversations(?string $status = null): LengthAwarePaginator
    {
        $query = Conversation::where('customer_id', Auth::id())
            ->with(['hotel:id,name', 'assignedTo:id,name', 'latestMessage.sender:id,name']);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->latest()->paginate(15);
    }

    public function getCustomerConversation(int $conversationId): Conversation
    {
        return Conversation::where('customer_id', Auth::id())
            ->where('id', $conversationId)
            ->with(['hotel', 'assignedTo:id,name,email', 'messages.sender:id,name'])
            ->firstOrFail();
    }

    public function sendMessage(int $conversationId, array $data): Message
    {
        $conversation = Conversation::where('customer_id', Auth::id())
            ->where('id', $conversationId)
            ->firstOrFail();

        // Update conversation status to active if it was open
        if ($conversation->status === 'open') {
            $conversation->update(['status' => 'active']);
        }

        return Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => Auth::id(),
            'message'         => $data['message'],
            'attachments'     => $data['attachments'] ?? null,
        ]);
    }

    public function closeConversation(int $conversationId): Conversation
    {
        $conversation = Conversation::where('customer_id', Auth::id())
            ->where('id', $conversationId)
            ->firstOrFail();

        $conversation->update(['status' => 'closed']);
        $conversation->refresh();

        return $conversation;
    }

    // Hotel owner methods
    public function getHotelConversations(int $hotelId, ?string $status = null): LengthAwarePaginator
    {
        // Verify hotel ownership
        \App\Models\Hotel::where('owner_id', Auth::id())
            ->where('id', $hotelId)
            ->firstOrFail();

        $query = Conversation::where('hotel_id', $hotelId)
            ->with(['customer:id,name,email', 'assignedTo:id,name', 'latestMessage.sender:id,name']);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->latest()->paginate(15);
    }

    public function getAssignedConversations(?string $status = null): LengthAwarePaginator
    {
        $query = Conversation::where('assigned_to', Auth::id())
            ->with(['customer:id,name,email', 'hotel:id,name', 'latestMessage.sender:id,name']);

        if ($status) {
            $query->where('status', $status);
        }

        return $query->latest()->paginate(15);
    }

    public function getHotelConversation(int $hotelId, int $conversationId): Conversation
    {
        // Verify hotel ownership
        \App\Models\Hotel::where('owner_id', Auth::id())
            ->where('id', $hotelId)
            ->firstOrFail();

        return Conversation::where('hotel_id', $hotelId)
            ->where('id', $conversationId)
            ->with(['customer:id,name,email', 'assignedTo:id,name,email', 'messages.sender:id,name'])
            ->firstOrFail();
    }

    public function replyAsHotelOwner(int $hotelId, int $conversationId, array $data): Message
    {
        // Verify hotel ownership
        \App\Models\Hotel::where('owner_id', Auth::id())
            ->where('id', $hotelId)
            ->firstOrFail();

        $conversation = Conversation::where('hotel_id', $hotelId)
            ->where('id', $conversationId)
            ->firstOrFail();

        // Auto-assign if not assigned
        if (! $conversation->assigned_to) {
            $conversation->update(['assigned_to' => Auth::id()]);
        }

        // Update status
        if ($conversation->status === 'open') {
            $conversation->update(['status' => 'active']);
        }

        return Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => Auth::id(),
            'message'         => $data['message'],
            'attachments'     => $data['attachments'] ?? null,
        ]);
    }

    // Admin methods
    public function getAllConversations(array $filters): LengthAwarePaginator
    {
        $query = Conversation::with(['customer:id,name,email', 'hotel:id,name', 'assignedTo:id,name', 'latestMessage.sender:id,name']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['hotel_id'])) {
            $query->where('hotel_id', $filters['hotel_id']);
        }

        if (! empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        return $query->latest()->paginate(15);
    }

    public function assignConversation(int $conversationId, int $userId): Conversation
    {
        $conversation = Conversation::findOrFail($conversationId);

        $conversation->update(['assigned_to' => $userId]);
        $conversation->refresh();

        return $conversation;
    }
}
