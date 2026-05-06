<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    /**
     * Otorisasi untuk admin & superadmin.
     */
    private function authorizeAdmin()
    {
        if (!in_array(auth()->user()->role->slug, ['admin', 'superadmin'])) {
            abort(403, 'Akses ditolak.');
        }
    }

    /**
     * Otorisasi khusus superadmin.
     */
    private function authorizeSuperadmin()
    {
        if (auth()->user()->role->slug !== 'superadmin') {
            abort(403, 'Hanya Super Admin yang dapat melakukan aksi ini.');
        }
    }

    /**
     * Menampilkan halaman daftar role dan permission.
     */
    public function index()
    {
        $this->authorizeAdmin();
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all()->groupBy('module');
        return view('roles.index', compact('roles', 'permissions'));
    }

    /**
     * Mengambil data role beserta permission-nya dalam format JSON (untuk modal/detail).
     */
    public function edit(Role $role)
    {
        $this->authorizeSuperadmin();
        $role->load('permissions');
        return response()->json($role);
    }

    /**
     * Menambahkan role baru.
     */
    public function store(Request $request)
    {
        $this->authorizeSuperadmin();
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles',
            'slug' => 'required|string|max:255|unique:roles',
        ]);

        Role::create($validated);
        return response()->json(['success' => true, 'message' => 'Role berhasil ditambahkan.']);
    }

    /**
     * Memperbarui data role (nama & slug).
     */
    public function update(Request $request, Role $role)
    {
        $this->authorizeSuperadmin();
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'slug' => 'required|string|max:255|unique:roles,slug,' . $role->id,
        ]);

        $role->update($validated);
        return response()->json(['success' => true, 'message' => 'Role berhasil diperbarui.']);
    }

    /**
     * Menyinkronkan permission untuk suatu role.
     */
    public function syncPermissions(Request $request, Role $role)
    {
        $this->authorizeSuperadmin();
        $permissions = $request->input('permissions', []);
        $role->permissions()->sync($permissions);
        return response()->json(['success' => true, 'message' => 'Permission berhasil disimpan.']);
    }

    /**
     * Menghapus role (kecuali role bawaan).
     */
    public function destroy(Role $role)
    {
        $this->authorizeSuperadmin();
        // Cegah penghapusan role bawaan
        if (in_array($role->slug, ['superadmin', 'admin', 'petugas', 'mahasiswa'])) {
            return response()->json([
                'success' => false,
                'message' => 'Role bawaan tidak dapat dihapus.'
            ], 422);
        }

        $role->delete();
        return response()->json(['success' => true, 'message' => 'Role berhasil dihapus.']);
    }
}
