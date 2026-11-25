<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
class AccountsPermissionSeeder extends Seeder {
 public function run(): void {
  app(PermissionRegistrar::class)->forgetCachedPermissions();
  $names=['accounts.view','accounts.expense.create','accounts.category.manage','accounts.expense.pay','accounts.expense.approve'];
  foreach($names as $name) Permission::findOrCreate($name,'web');
  $role=Role::findOrCreate('Accountant','web');$role->givePermissionTo(array_slice($names,0,4));
  foreach(['Admin','Super Admin'] as $name) { $role=Role::where('name',$name)->where('guard_name','web')->first(); if($role) $role->givePermissionTo($names); }
  app(PermissionRegistrar::class)->forgetCachedPermissions();
 }
}
