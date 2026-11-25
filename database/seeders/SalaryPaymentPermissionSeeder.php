<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
class SalaryPaymentPermissionSeeder extends Seeder {public function run(){foreach(['accounts.salary.view','accounts.salary.pay'] as $name){$p=Permission::findOrCreate($name,'web');foreach(['Accountant','Admin','Super Admin'] as $role){Role::where('name',$role)->where('guard_name','web')->first()?->givePermissionTo($p);}}}}
