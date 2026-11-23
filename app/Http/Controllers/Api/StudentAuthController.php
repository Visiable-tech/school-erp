<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\StudentAppAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
class StudentAuthController extends Controller {
 public function login(Request $r){$v=$r->validate(['login_id'=>'required|string|max:50','password'=>'required|string','school_id'=>'required|integer|exists:schools,id']);
 $a=StudentAppAccount::where('school_id',$v['school_id'])->where('login_id',$v['login_id'])->first();
 if(!$a || !$a->is_active || !Hash::check($v['password'],$a->password) || !$a->student || !$a->student->status){throw ValidationException::withMessages(['login_id'=>'Invalid login credentials or inactive account.']);}
 $a->update(['last_login_at'=>now()]);$token=$a->createToken('student-mobile',['student:mobile'])->plainTextToken;
 return response()->json(['token'=>$token,'token_type'=>'Bearer','must_change_password'=>$a->must_change_password,'student'=>['id'=>$a->student->id,'name'=>$a->student->student_name,'admission_no'=>$a->student->admission_no]]);
 }
 private function account(Request $r):StudentAppAccount {$a=$r->user();abort_unless($a instanceof StudentAppAccount && $a->is_active && $a->student && $a->student->status,403);return $a;}
 
 public function profile(Request $request)
{
    $start = microtime(true);

    $account = $request->user();

    $student = $account->student;

    $duration = round(
        (microtime(true) - $start) * 1000,
        2
    );

    return response()->json([
        'id' => $student->id,
        'name' => $student->student_name,
        'admission_no' => $student->admission_no,
        'must_change_password' => $account->must_change_password,
        'execution_time_ms' => $duration,
    ]);
}
 
 public function changePassword(Request $r){$a=$this->account($r);$v=$r->validate(['current_password'=>'required|string','password'=>'required|string|min:10|confirmed']);if(!Hash::check($v['current_password'],$a->password))throw ValidationException::withMessages(['current_password'=>'Current password is incorrect.']);$a->update(['password'=>$v['password'],'must_change_password'=>false]);$a->tokens()->where('id','!=',$a->currentAccessToken()->id)->delete();return response()->json(['message'=>'Password changed.']);}
 public function logout(Request $r){$this->account($r)->currentAccessToken()->delete();return response()->json(['message'=>'Logged out.']);}
}
