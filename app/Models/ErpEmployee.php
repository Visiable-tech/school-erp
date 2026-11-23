<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ErpEmployee extends Model {
 protected $table='erp_employees';protected $guarded=[];
 protected $casts=['dob'=>'date','joining_date'=>'date','passport_expiry_date'=>'date','probation_ends_on'=>'date','pf_appear_date'=>'date','confirmation_date'=>'date','address_from_date'=>'date','date_of_separation'=>'date','bank_from_date'=>'date','pf_activation_date'=>'date','exclude_ptax'=>'boolean','rejoin'=>'boolean','exclude_from_substitution'=>'boolean','create_user_login'=>'boolean','skip_salary_generation'=>'boolean','bank_other_org_accounts'=>'boolean','same_as_correspondence'=>'boolean','allow_marking_locked_attendance'=>'boolean','is_principal'=>'boolean'];
 public function user(){return $this->belongsTo(User::class,'user_id');}
  public function events(){return $this->hasMany(ErpEmployeeEvent::class,'employee_id');}
 public function certificates(){return $this->hasMany(ErpEmployeeCertificate::class,'employee_id');}
}
