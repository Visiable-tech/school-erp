<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentProfileModifyRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'student_id',
        'request_date',
        'requested_changes',
        'request_remarks',
        'status',
        'requested_by',
        'reviewed_by',
        'reviewed_at',
        'review_remarks',
    ];

    protected $casts = [
        'request_date' => 'date',
        'requested_changes' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(
            User::class,
            'requested_by'
        );
    }

    public function reviewedBy()
    {
        return $this->belongsTo(
            User::class,
            'reviewed_by'
        );
    }
}