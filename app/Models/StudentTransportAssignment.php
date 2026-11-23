<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class StudentTransportAssignment extends Model {protected $fillable=['school_id','student_id','student_enrollment_id','academic_year_id','transport_route_id','transport_route_stop_id','starts_on','ends_on','monthly_amount'];protected $casts=['starts_on'=>'date','ends_on'=>'date'];public function route(){return $this->belongsTo(TransportRoute::class,'transport_route_id');}public function stop(){return $this->belongsTo(TransportRouteStop::class,'transport_route_stop_id');}public function student(){return $this->belongsTo(Student::class);} }
