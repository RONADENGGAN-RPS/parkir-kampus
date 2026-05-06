<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $superadmin = Role::create(['name' => 'Super Admin', 'slug' => 'superadmin']);
        $admin = Role::create(['name' => 'Admin', 'slug' => 'admin']);
        $petugas = Role::create(['name' => 'Petugas', 'slug' => 'petugas']);
        $mahasiswa = Role::create(['name' => 'Mahasiswa', 'slug' => 'mahasiswa']);

        $modules = ['user', 'vehicle', 'parking', 'report', 'backup', 'setting'];
        $actions = ['create', 'read', 'update', 'delete', 'export', 'backup', 'restore', 'approve'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::create(['module' => $module, 'action' => $action]);
            }
        }

        $superadmin->permissions()->sync(Permission::all()->pluck('id'));

        $adminPerms = Permission::whereIn('module', ['user', 'vehicle', 'parking', 'report'])
            ->whereIn('action', ['create', 'read', 'update', 'delete', 'export'])->pluck('id');
        $admin->permissions()->sync($adminPerms);

        $petugasPerms = Permission::where('module', 'parking')
            ->whereIn('action', ['read', 'update', 'approve'])->pluck('id');
        $petugas->permissions()->sync($petugasPerms);

        $mhsPerms = Permission::where('module', 'vehicle')->where('action', 'read')->pluck('id');
        $mahasiswa->permissions()->sync($mhsPerms);
    }
}
