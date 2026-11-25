<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
class AccountsReportPermissionSeeder extends Seeder {public function run(){ $p=Permission::findOrCreate('accounts.reports.view','web');foreach(Role::where('guard_name','web')->whereIn('name',['Accountant','Admin','Super Admin'])->get() as $r)$r->givePermissionTo($p);}}
