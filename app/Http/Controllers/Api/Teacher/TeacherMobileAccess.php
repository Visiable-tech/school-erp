<?php
namespace App\Http\Controllers\Api\Teacher;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Laravel\Sanctum\PersonalAccessToken;
class TeacherMobileAccess {
 public function handle(Request $r, Closure $next, ?string $permission=null){
 $u=$r->user();abort_unless($u instanceof User && $u->status && $u->school_id && $u->hasRole('Teacher') && $r->bearerToken() && $u->currentAccessToken() instanceof PersonalAccessToken && $u->tokenCan('teacher') && DB::table('schools')->where('id',$u->school_id)->where('status',1)->exists(),403);
 if($permission)abort_unless($u->can($permission),403);
 return $next($r);
 }
}
