<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TransportStopRate extends Model {
 protected $fillable=['transport_route_stop_id','academic_year_id','monthly_amount'];
 public function stop(){return $this->belongsTo(TransportRouteStop::class,'transport_route_stop_id');}
}
