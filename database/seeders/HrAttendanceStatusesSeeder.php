<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class HrAttendanceStatusesSeeder extends Seeder {
 public function run():void {
  $schoolId=(int)config('hr_payroll.seed_school_id',0);
  if(!$schoolId){$this->command?->warn('Set HR_SEED_SCHOOL_ID in .env before seeding.');return;}
  if(!DB::table('schools')->where('id',$schoolId)->exists())throw new \RuntimeException('Invalid school ID');
  $items=[
   ['Present','P','present'],['Absent','Ab','absent'],['Leave','L','leave'],
   ['Late','Lt','late'],['Off','O','off'],['EWD Half','EH','half_day'],
   ['EWD Full','EF','present'],['Compensatory Off','CO','off'],
   ['Half Day (Ab)','HDA','half_day'],['Half Day (L)','HDL','half_day'],
   ['Absent + Leave','AL','leave'],['On Duty','OD','on_duty'],
   ['Holiday','HL','holiday'],['NA','NA','other'],
   ['Non Paid Off','NPO','off'],['Suspend','SUS','absent'],
   ['Short Lv.+ Present','SLP','present'],['Short Lv.+ Absent','SLA','absent'],
  ];
  foreach($items as [$name,$code,$countAs]){
   DB::table('hr_attendance_statuses')->updateOrInsert(
    ['school_id'=>$schoolId,'code'=>$code],
    ['name'=>$name,'count_as'=>$countAs,'is_active'=>true,
     'updated_at'=>now(),'created_at'=>now()]
   );
  }
 }
}
