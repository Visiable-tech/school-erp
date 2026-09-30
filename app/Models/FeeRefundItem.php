<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class FeeRefundItem extends Model {
 use HasFactory;
 protected $fillable=['school_id','fee_refund_id','fee_collection_item_id','student_fee_due_id','fee_head_name','installment_name','due_date','original_paid_amount','refund_amount'];
 protected $casts=['due_date'=>'date','original_paid_amount'=>'decimal:2','refund_amount'=>'decimal:2'];
 public function refund(){return $this->belongsTo(FeeRefund::class,'fee_refund_id');} public function collectionItem(){return $this->belongsTo(FeeCollectionItem::class,'fee_collection_item_id');} public function due(){return $this->belongsTo(StudentFeeDue::class,'student_fee_due_id');}
}
