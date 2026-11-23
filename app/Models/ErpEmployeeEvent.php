<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ErpEmployeeEvent extends Model {
 protected $table='erp_employee_events';protected $guarded=[];protected $casts=['event_date'=>'date','end_date'=>'date'];
 public function employee(){return $this->belongsTo(ErpEmployee::class,'employee_id');}
}
