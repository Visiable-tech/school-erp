<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSuspension extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'student_id',
        'student_enrollment_id',
        'suspension_from',
        'suspension_to',
        'reason',
        'remarks',
        'status',
        'created_by',
        'revoked_by',
        'revoked_at',
        'revocation_remarks',
    ];

    protected $casts = [
        'suspension_from' => 'date',
        'suspension_to' => 'date',
        'revoked_at' => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
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

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function revokedBy()
    {
        return $this->belongsTo(
            User::class,
            'revoked_by'
        );
    }
}