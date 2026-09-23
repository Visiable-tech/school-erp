<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdmissionApplicationDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'admission_application_id',
        'document_type',
        'document_name',
        'document_number',
        'file_path',
        'verification_status',
        'verification_remarks',
        'verified_by',
        'verified_at',
        'uploaded_by',
        'status',
    ];

    protected $casts = [
        'verified_at' => 'datetime',
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

    public function verifier()
    {
        return $this->belongsTo(
            User::class,
            'verified_by'
        );
    }

    public function uploader()
    {
        return $this->belongsTo(
            User::class,
            'uploaded_by'
        );
    }
}