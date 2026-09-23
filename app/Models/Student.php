<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [

        'school_id',
        'admission_application_id',
        'admission_no',
        'admission_date',

        'student_name',
        'date_of_birth',
        'gender',
        'blood_group',
        'nationality',
        'religion',
        'category',
        'mother_tongue',
        'student_photo',

        'father_name',
        'father_mobile',
        'father_email',
        'father_occupation',

        'mother_name',
        'mother_mobile',
        'mother_email',
        'mother_occupation',

        'guardian_name',
        'guardian_relation',
        'guardian_mobile',

        'present_address',
        'present_city',
        'present_state',
        'present_pin_code',

        'permanent_address',
        'permanent_city',
        'permanent_state',
        'permanent_pin_code',

        'previous_school',
        'previous_class',
        'previous_board',

        'created_by',
        'status',
    ];

    protected $casts = [
        'admission_date' => 'date',
        'date_of_birth' => 'date',
        'status' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function application()
    {
        return $this->belongsTo(
            AdmissionApplication::class,
            'admission_application_id'
        );
    }

    public function enrollments()
    {
        return $this->hasMany(
            StudentEnrollment::class
        );
    }

    public function currentEnrollment()
    {
        return $this->hasOne(
            StudentEnrollment::class
        )->where('is_current', true);
    }

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

    public function documents()
    {
        return $this->hasMany(StudentDocument::class);
    }

    public function attendances()
    {
        return $this->hasMany(StudentAttendance::class);
    }

}