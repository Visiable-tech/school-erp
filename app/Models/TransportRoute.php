<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TransportRoute extends Model {protected $fillable=['school_id','code','name','status'];public function stops(){return $this->hasMany(TransportRouteStop::class)->orderBy('sort_order');}public function rates(){return $this->hasMany(TransportRouteRate::class);} }
