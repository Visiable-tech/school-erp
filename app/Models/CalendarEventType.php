<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarEventType extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'is_holiday',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'is_holiday' => 'boolean',
        'status' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function calendarEvents()
    {
        return $this->hasMany(
            SchoolCalendarEvent::class,
            'calendar_event_type_id'
        );
    }
}