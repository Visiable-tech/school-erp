<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class FeeRefund extends Model {
 use HasFactory;
 protected $fillable=['school_id','academic_year_id','student_id','student_enrollment_id','fee_collection_id','refund_no','refund_date','total_amount','reason','processed_by','status','cancellation_reason','cancelled_by','cancelled_at'];
 protected $casts=['refund_date'=>'date','total_amount'=>'decimal:2','cancelled_at'=>'datetime'];
 public function school(){return $this->belongsTo(School::class);} public function academicYear(){return $this->belongsTo(AcademicYear::class);} public function student(){return $this->belongsTo(Student::class);} public function enrollment(){return $this->belongsTo(StudentEnrollment::class,'student_enrollment_id');} public function collection(){return $this->belongsTo(FeeCollection::class,'fee_collection_id');} public function items(){return $this->hasMany(FeeRefundItem::class,'fee_refund_id');} public function processedBy(){return $this->belongsTo(User::class,'processed_by');} public function cancelledBy(){return $this->belongsTo(User::class,'cancelled_by');}
}
