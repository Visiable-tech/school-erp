<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
class LedgerPermissionSeeder extends Seeder {public function run(){ $p=['accounts.ledger.view','accounts.ledger.create','accounts.ledger.approve'];foreach($p as $n) Permission::findOrCreate($n,'web');Role::findOrCreate('Accountant','web')->givePermissionTo(array_slice($p,0,2));foreach(Role::where('guard_name','web')->whereIn('name',['Admin','Super Admin'])->get() as $r)$r->givePermissionTo($p);}}
