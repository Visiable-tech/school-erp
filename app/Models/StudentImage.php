<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentImage extends Model
{
    use HasFactory;

    public const TYPE_STUDENT = 'student';
    public const TYPE_FATHER = 'father';
    public const TYPE_MOTHER = 'mother';
    public const TYPE_GUARDIAN = 'guardian';
    public const TYPE_GLIMPSE_MYSELF = 'glimpse_myself';
    public const TYPE_GLIMPSE_FAMILY = 'glimpse_family';
    public const TYPE_LEARNER_PORTFOLIO = 'learner_portfolio';

    protected $fillable = [
        'school_id',
        'student_id',
        'image_type',
        'image_path',
        'created_by',
    ];

    public static function types(): array
    {
        return [
            self::TYPE_STUDENT,
            self::TYPE_FATHER,
            self::TYPE_MOTHER,
            self::TYPE_GUARDIAN,
            self::TYPE_GLIMPSE_MYSELF,
            self::TYPE_GLIMPSE_FAMILY,
            self::TYPE_LEARNER_PORTFOLIO,
        ];
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }
}