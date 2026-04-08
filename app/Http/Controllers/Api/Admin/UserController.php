<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * List all users (customers, partners, staff)
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::query();

        // Filter by role
        if ($request->has('role')) {
            $query->where('global_role', $request->role);
        }

        // Filter by active status
        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Search by name or email
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->withCount(['ownedHotels', 'bookings'])
            ->latest()
            ->paginate(20);

        return $this->success('Users retrieved.', $users);
    }

    /**
     * Create a new user (customer, partner, or staff)
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'email'                 => 'required|email|unique:users,email',
            'phone'                 => 'nullable|string|max:30',
            'password'              => 'required|string|min:8',
            'global_role'           => ['required', 'string', Rule::in(['customer', 'partner', 'admin'])],
            'is_active'             => 'nullable|boolean',
        ]);

        $user = User::create([
            'name'                => $validated['name'],
            'email'               => $validated['email'],
            'phone'               => $validated['phone'] ?? null,
            'password'            => Hash::make($validated['password']),
            'global_role'         => $validated['global_role'],
            'registration_source' => 'admin_panel',
            'is_active'           => $validated['is_active'] ?? true,
            'email_verified_at'   => now(), // Auto-verify when admin creates users
        ]);

        return $this->success('User created successfully.', $user->fresh(), 201);
    }

    /**
     * Get user details
     */
    public function show(int $id): JsonResponse
    {
        $user = User::with(['ownedHotels:id,name,city,status', 'bookings:id,hotel_id,status'])
            ->findOrFail($id);

        return $this->success('User retrieved.', $user);
    }

    /**
     * Update user details
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'      => 'sometimes|string|max:255',
            'email'     => 'sometimes|email|unique:users,email,' . $id,
            'phone'     => 'nullable|string|max:30',
            'is_active' => 'sometimes|boolean',
        ]);

        $user->update($validated);

        return $this->success('User updated successfully.', $user->fresh());
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        // Prevent deactivating yourself
        if ($user->id === auth()->id()) {
            return $this->error('Cannot deactivate your own account.', 400);
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';
        return $this->success("User {$status} successfully.", $user->fresh());
    }

    /**
     * Delete user
     */
    public function destroy(int $id): JsonResponse
    {
        $user = User::findOrFail($id);

        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return $this->error('Cannot delete your own account.', 400);
        }

        $user->delete();

        return $this->success('User deleted successfully.');
    }
}
