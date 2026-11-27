<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
class TeacherStudentNoticePermissionSeeder extends Seeder {
 public function run(): void {
 $names=['teacher-notice.view','teacher-notice.create','teacher-notice.publish','teacher-notice.school-wide','teacher-notice.manage'];
 foreach($names as $n) Permission::findOrCreate($n,'web');
 foreach(['Teacher','Admin','Super Admin'] as $name){$role=Role::where('name',$name)->where('guard_name','web')->first();if($role)$role->givePermissionTo($name==='Teacher'?array_slice($names,0,3):$names);}
 app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
 }
}
