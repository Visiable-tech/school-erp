<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TransportRouteStop extends Model {
 protected $fillable=['transport_route_id','name','sort_order','status'];
 public function route(){return $this->belongsTo(TransportRoute::class,'transport_route_id');}
 public function rates(){return $this->hasMany(TransportStopRate::class,'transport_route_stop_id');}
}
