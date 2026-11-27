<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\{Role,Permission};
use Spatie\Permission\PermissionRegistrar;
class FrontOfficePermissionSeeder extends Seeder {
 public function run(): void {
 app(PermissionRegistrar::class)->forgetCachedPermissions();
 $permissions=['front-office.view','front-office.create','front-office.edit'];
 foreach($permissions as $p) Permission::findOrCreate($p,'web');
 Role::findOrCreate('Receptionist','web')->givePermissionTo($permissions);
 foreach(['Admin','Super Admin'] as $name) { $role=Role::where('name',$name)->where('guard_name','web')->first(); if($role) $role->givePermissionTo($permissions); }
 app(PermissionRegistrar::class)->forgetCachedPermissions();
 }
}
