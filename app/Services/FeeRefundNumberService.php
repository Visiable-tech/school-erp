<?php
namespace App\Services;
use App\Models\FeeRefund;
class FeeRefundNumberService {
 public function generate(int $schoolId): string {
  $last=FeeRefund::where('school_id',$schoolId)->lockForUpdate()->orderByDesc('id')->first();
  $next=$last ? $last->id+1 : 1;
  return 'REF-'.date('Y').'-'.str_pad($next,6,'0',STR_PAD_LEFT);
 }
}
