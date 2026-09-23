<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'attendance_session_id',
        'student_id',
        'student_enrollment_id',
        'attendance_status',
        'remarks',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function attendanceSession()
    {
        return $this->belongsTo(
            StudentAttendanceSession::class,
            'attendance_session_id'
        );
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function enrollment()
    {
        return $this->belongsTo(
            StudentEnrollment::class,
            'student_enrollment_id'
        );
    }
}