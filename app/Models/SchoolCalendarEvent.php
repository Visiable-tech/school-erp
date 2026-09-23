<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolCalendarEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'calendar_event_type_id',
        'title',
        'description',
        'start_at',
        'end_at',
        'is_all_day',
        'is_holiday',
        'status',
    ];

    protected $casts = [
        'start_at'   => 'datetime',
        'end_at'     => 'datetime',
        'is_all_day' => 'boolean',
        'is_holiday' => 'boolean',
        'status'     => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function eventType()
    {
        return $this->belongsTo(
            CalendarEventType::class,
            'calendar_event_type_id'
        );
    }
}