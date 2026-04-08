@extends('admin.layouts.app')

@section('title', 'Support Chats')
@section('page-title', 'Support Conversations')

@section('content')
<!-- Filter -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-6">
    <div class="flex items-center gap-4 flex-wrap">
        <a href="?status=all" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status', 'all') === 'all' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">All</a>
        <a href="?status=open" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') === 'open' ? 'bg-amber-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Open</a>
        <a href="?status=active" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') === 'active' ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Active</a>
        <a href="?status=closed" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') === 'closed' ? 'bg-gray-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">Closed</a>
    </div>
</div>

<!-- Conversations List -->
<div class="space-y-4">
    @forelse($conversations as $conv)
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
        <div class="flex items-start justify-between">
            <div class="flex items-start gap-4 flex-1 min-w-0">
                <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center shrink-0">
                    <i class="fas fa-user text-primary-600"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-1">
                        <h4 class="font-semibold text-gray-800 truncate">{{ $conv->customer->name }}</h4>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium
                            @if($conv->status === 'open') bg-amber-100 text-amber-700
                            @elseif($conv->status === 'active') bg-blue-100 text-blue-700
                            @else bg-gray-100 text-gray-600 @endif">
                            {{ ucfirst($conv->status) }}
                        </span>
                    </div>
                    @if($conv->subject)
                    <p class="text-sm text-gray-600 mb-1">{{ $conv->subject }}</p>
                    @endif
                    @if($conv->hotel)
                    <p class="text-xs text-gray-400"><i class="fas fa-building mr-1"></i>{{ $conv->hotel->name }}</p>
                    @endif
                    @if($conv->latestMessage)
                    <p class="text-sm text-gray-500 mt-2 truncate">
                        <span class="font-medium text-gray-700">{{ $conv->latestMessage->sender->name }}:</span>
                        {{ $conv->latestMessage->message }}
                    </p>
                    @endif
                </div>
            </div>
            <div class="text-right shrink-0 ml-4">
                <p class="text-xs text-gray-400">{{ $conv->created_at->diffForHumans() }}</p>
                @if($conv->assignedTo)
                <p class="text-xs text-gray-500 mt-1"><i class="fas fa-user-tag mr-1"></i>{{ $conv->assignedTo->name }}</p>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 py-16 text-center text-gray-400">
        <i class="fas fa-comments text-4xl mb-3 block"></i>
        No conversations yet
    </div>
    @endforelse
</div>

@if($conversations->hasPages())
<div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-100 px-6 py-4">
    {{ $conversations->links() }}
</div>
@endif
@endsection
