<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicYear extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'start_date',
        'end_date',
        'is_current',
        'status',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'is_current'  => 'boolean',
        'status'      => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class);
    }

    public function sectionGroups()
    {
        return $this->hasMany(SectionGroup::class);
    }

    public function calendarEvents()
    {
        return $this->hasMany(SchoolCalendarEvent::class);
    }

    public function admissionSessions()
    {
        return $this->hasMany(
            AdmissionSession::class
        );
    }

    public function admissionEnquiries()
    {
        return $this->hasMany(
            AdmissionEnquiry::class
        );
    }
}