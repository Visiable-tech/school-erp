<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmissionNumberScheme extends Model
{
    public const TYPES = [
        'enquiry' => 'Enquiry No.',
        'form' => 'Form No.',
        'registration' => 'Registration No.',
        'form_sale_receipt' => 'Form Sale Receipt',
        'registration_receipt' => 'Registration Receipt',
    ];

    protected $fillable = [
        'school_id',
        'scheme_type',
        'scheme_name',
        'auto_generate',
        'include_academic_year',
        'include_class',
        'separator',
        'fixed_string',
        'starting_number',
        'last_generated_number',
        'number_length',
        'allow_manual_entry',
        'status',
    ];

    protected $casts = [
        'auto_generate' => 'boolean',
        'include_academic_year' => 'boolean',
        'include_class' => 'boolean',
        'allow_manual_entry' => 'boolean',
        'status' => 'boolean',
        'starting_number' => 'integer',
        'last_generated_number' => 'integer',
        'number_length' => 'integer',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}