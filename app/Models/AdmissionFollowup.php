<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionFollowup extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'admission_enquiry_id',
        'followup_date',
        'followup_type',
        'contact_person',
        'response_status',
        'remarks',
        'next_followup_date',
        'followed_by',
        'status',
    ];

    protected $casts = [
        'followup_date' => 'date',
        'next_followup_date' => 'date',
        'status' => 'boolean',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function enquiry()
    {
        return $this->belongsTo(
            AdmissionEnquiry::class,
            'admission_enquiry_id'
        );
    }

    public function followedBy()
    {
        return $this->belongsTo(
            User::class,
            'followed_by'
        );
    }
}