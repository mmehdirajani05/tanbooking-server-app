@extends('admin.layouts.app')

@section('title', 'Hotels')
@section('page-title', 'Hotel Management')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center gap-2 flex-wrap">
            <a href="?status=all" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status', 'all') === 'all' ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">All</a>
            <a href="?status=pending" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') === 'pending' ? 'bg-amber-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"><i class="fas fa-clock mr-1"></i> Pending</a>
            <a href="?status=approved" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') === 'approved' ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"><i class="fas fa-check mr-1"></i> Approved</a>
            <a href="?status=rejected" class="px-4 py-2 rounded-lg text-sm font-medium transition {{ request('status') === 'rejected' ? 'bg-red-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"><i class="fas fa-times mr-1"></i> Rejected</a>
        </div>
    </div>
    <a href="{{ route('admin.hotels.create') }}" class="bg-primary-600 text-white px-5 py-2.5 rounded-xl hover:bg-primary-700 transition font-medium text-sm">
        <i class="fas fa-plus mr-2"></i>Create Hotel
    </a>
</div>

    <!-- Hotels Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500">
                    <tr>
                        <th class="text-left px-6 py-4 font-medium">Hotel</th>
                        <th class="text-left px-4 py-4 font-medium">City</th>
                        <th class="text-left px-4 py-4 font-medium">Owner</th>
                        <th class="text-left px-4 py-4 font-medium">Rooms</th>
                        <th class="text-left px-4 py-4 font-medium">Status</th>
                        <th class="text-left px-4 py-4 font-medium">Date</th>
                        <th class="text-right px-6 py-4 font-medium">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($hotels as $hotel)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-primary-100 rounded-lg flex items-center justify-center shrink-0">
                                    <i class="fas fa-building text-primary-600"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-medium text-gray-800 truncate">{{ $hotel->name }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ $hotel->area }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-4 text-gray-600">{{ $hotel->city }}</td>
                        <td class="px-4 py-4">
                            <p class="text-gray-800">{{ $hotel->owner->name }}</p>
                            <p class="text-xs text-gray-400">{{ $hotel->owner->email }}</p>
                        </td>
                        <td class="px-4 py-4 text-gray-600">{{ $hotel->room_types_count ?? 0 }}</td>
                        <td class="px-4 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                @if($hotel->status === 'approved') bg-emerald-100 text-emerald-700
                                @elseif($hotel->status === 'pending') bg-amber-100 text-amber-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ ucfirst($hotel->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-4 text-gray-500 text-xs">{{ $hotel->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.hotels.detail', $hotel->id) }}" class="text-xs bg-primary-100 text-primary-700 px-3 py-1.5 rounded-lg hover:bg-primary-200 transition">
                                    <i class="fas fa-eye mr-1"></i>View
                                </a>
                                <a href="{{ route('admin.hotels.edit', $hotel->id) }}" class="text-xs bg-gray-100 text-gray-700 px-3 py-1.5 rounded-lg hover:bg-gray-200 transition">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                                @if($hotel->status === 'pending')
                                <form method="POST" action="{{ route('admin.hotels.approve', $hotel->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs bg-emerald-500 text-white px-3 py-1.5 rounded-lg hover:bg-emerald-600 transition">
                                        <i class="fas fa-check mr-1"></i>Approve
                                    </button>
                                </form>
                                <button onclick="document.getElementById('rejectModal{{ $hotel->id }}').showModal()" class="text-xs bg-red-500 text-white px-3 py-1.5 rounded-lg hover:bg-red-600 transition">
                                    <i class="fas fa-times mr-1"></i>Reject
                                </button>

                                <dialog id="rejectModal{{ $hotel->id }}" class="rounded-xl p-0 shadow-2xl backdrop:bg-black/50">
                                    <div class="bg-white p-6 w-96">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Reject Hotel</h3>
                                        <form method="POST" action="{{ route('admin.hotels.reject', $hotel->id) }}">
                                            @csrf
                                            <label class="block text-sm text-gray-600 mb-2">Reason (optional)</label>
                                            <textarea name="reason" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent"
                                                placeholder="Reason for rejection..."></textarea>
                                            <div class="flex gap-2 mt-4">
                                                <button type="button" onclick="document.getElementById('rejectModal{{ $hotel->id }}').close()" class="flex-1 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 text-sm">Cancel</button>
                                                <button type="submit" class="flex-1 px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600 text-sm">Reject</button>
                                            </div>
                                        </form>
                                    </div>
                                </dialog>
                                @endif

                                @if($hotel->status === 'rejected')
                                <form method="POST" action="{{ route('admin.hotels.approve', $hotel->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="text-xs bg-emerald-500 text-white px-3 py-1.5 rounded-lg hover:bg-emerald-600 transition">
                                        <i class="fas fa-redo mr-1"></i>Re-approve
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-6 py-12 text-center text-gray-400">
                        <i class="fas fa-building text-4xl mb-3 block"></i>
                        No hotels found
                    </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($hotels->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $hotels->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
