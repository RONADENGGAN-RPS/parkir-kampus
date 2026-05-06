<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    private function authorizeAdmin()
    {
        if (!in_array(auth()->user()->role->slug, ['admin', 'superadmin'])) {
            abort(403, 'Akses ditolak.');
        }
    }

    public function index(Request $request)
    {
        $this->authorizeAdmin();

        $query = User::with('role');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('email', 'like', "%$search%")
                    ->orWhere('nim', 'like', "%$search%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role_id', $request->role);
        }

        $users = $query->latest()->get();
        $roles = Role::all();

        // Permission user saat ini (untuk view)
        $userPerms = auth()->user()->role->permissions
            ->where('module', 'user')
            ->pluck('action');

        return view('users.index', compact('users', 'roles', 'userPerms'));
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();

        // Admin tidak boleh membuat Super Admin
        if (
            auth()->user()->role->slug === 'admin' &&
            $request->role_id == Role::where('slug', 'superadmin')->value('id')
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menambah user sebagai Super Admin.'
            ], 403);
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'nim'      => 'nullable|unique:users',
            'role_id'  => 'required|exists:roles,id',
            'password' => 'required|min:6',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['active'] = true;
        $validated['created_by'] = auth()->id();

        User::create($validated);
        return response()->json(['success' => true, 'message' => 'Pengguna berhasil ditambahkan.']);
    }

    public function edit(User $user)
    {
        $this->authorizeAdmin();
        return response()->json($user);
    }

    public function update(Request $request, User $user)
    {
        $this->authorizeAdmin();

        // Admin tidak bisa edit Super Admin
        if (auth()->user()->role->slug === 'admin' && $user->role->slug === 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat mengedit Super Admin.'
            ], 403);
        }

        // Admin tidak bisa mengubah role menjadi Super Admin
        if (
            auth()->user()->role->slug === 'admin' &&
            $request->role_id == Role::where('slug', 'superadmin')->value('id')
        ) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat memberikan role Super Admin.'
            ], 403);
        }

        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'nim'      => ['nullable', Rule::unique('users')->ignore($user->id)],
            'role_id'  => 'required|exists:roles,id',
            'password' => 'nullable|min:6',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($validated['password']);
        }

        $validated['updated_by'] = auth()->id();
        $user->update($validated);
        return response()->json(['success' => true, 'message' => 'Data pengguna diperbarui.']);
    }

    public function destroy(User $user)
    {
        $this->authorizeAdmin();

        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak bisa menghapus diri sendiri.'
            ], 422);
        }

        // Admin tidak boleh hapus Super Admin
        if (auth()->user()->role->slug === 'admin' && $user->role->slug === 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menghapus Super Admin.'
            ], 422);
        }

        $user->delete();
        return response()->json(['success' => true, 'message' => 'Pengguna dihapus.']);
    }

    public function resetPassword(User $user)
    {
        $this->authorizeAdmin();

        // Admin tidak boleh reset password Super Admin
        if (auth()->user()->role->slug === 'admin' && $user->role->slug === 'superadmin') {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat mereset password Super Admin.'
            ], 422);
        }

        $user->update(['password' => Hash::make('password')]);
        return response()->json(['success' => true, 'message' => 'Password direset ke "password".']);
    }
}
