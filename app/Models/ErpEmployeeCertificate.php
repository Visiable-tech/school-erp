<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ErpEmployeeCertificate extends Model {
 protected $table='erp_employee_certificates';protected $guarded=[];protected $casts=['expiry_date'=>'date'];
 public function employee(){return $this->belongsTo(ErpEmployee::class,'employee_id');}
}
