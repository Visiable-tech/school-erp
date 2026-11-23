<?php
namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
class StudentAppAccount extends Authenticatable {
 use HasApiTokens;
 protected $fillable=['school_id','student_id','login_id','password','is_active','must_change_password','last_login_at'];
 protected $hidden=['password','remember_token'];
 protected $casts=['password'=>'hashed','is_active'=>'boolean','must_change_password'=>'boolean','last_login_at'=>'datetime'];
 public function student(){return $this->belongsTo(Student::class,'student_id');}
}
