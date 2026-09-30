<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_code',
        'school_name',
        'short_name',
        'email',
        'phone',
        'alternate_phone',
        'address',
        'city',
        'district',
        'state',
        'country',
        'pin_code',
        'board',
        'affiliation_no',
        'logo',
        'favicon',
        'website',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function academicYears()
    {
        return $this->hasMany(AcademicYear::class);
    }

    public function wings()
    {
        return $this->hasMany(Wing::class);
    }

    public function classes()
    {
        return $this->hasMany(SchoolClass::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    public function classSubjects()
    {
        return $this->hasMany(ClassSubject::class);
    }

    public function calendarEventTypes()
    {
        return $this->hasMany(CalendarEventType::class);
    }

    public function calendarEvents()
    {
        return $this->hasMany(SchoolCalendarEvent::class);
    }

    public function sectionGroups()
    {
        return $this->hasMany(SectionGroup::class);
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

    public function admissionFollowups()
    {
        return $this->hasMany(
            AdmissionFollowup::class
        );
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function studentEnrollments()
    {
        return $this->hasMany(
            StudentEnrollment::class
        );
    }

    public function studentAttendanceSessions()
    {
        return $this->hasMany(StudentAttendanceSession::class);
    }

    public function studentAttendances()
    {
        return $this->hasMany(StudentAttendance::class);
    }

    public function feeHeads()
    {
        return $this->hasMany(FeeHead::class);
    }

    public function studentFeeAssignments()
    {
        return $this->hasMany(StudentFeeAssignment::class);
    }

    public function studentFeeDues()
    {
        return $this->hasMany(StudentFeeDue::class);
    }

    public function feeCollections()
    {
        return $this->hasMany(FeeCollection::class);
    }

    public function feeCycles()
    {
        return $this->hasMany(FeeCycle::class);
    }   

    public function feeComponentGroups()
    {
        return $this->hasMany(FeeComponentGroup::class);
    }

    public function miscFeeComponents()
    {
        return $this->hasMany(
            MiscFeeComponent::class
        );
    }

    public function banks()
    {
        return $this->hasMany(
            BankMaster::class
        );
    }

    public function schoolAccounts()
    {
        return $this->hasMany(
            SchoolAccount::class
        );
    }

    public function feeReceiptSchemes()
    {
        return $this->hasMany(
            FeeReceiptScheme::class
        );
    }

    public function lateFeeFineRules()
    {
        return $this->hasMany(
            LateFeeFineRule::class
        );
    }

    public function paymentModes()
    {
        return $this->hasMany(PaymentMode::class);
    }

    public function chequeBounceReasons()
    {
        return $this->hasMany(
            ChequeBounceReason::class
        );
    }

    public function feeCompileQueues()
    {
        return $this->hasMany(
            FeeCompileQueue::class
        );
    }
}