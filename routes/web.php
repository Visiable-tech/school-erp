<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\WingController;
use App\Http\Controllers\SchoolClassController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ClassSubjectController;
use App\Http\Controllers\SectionGroupController;
use App\Http\Controllers\CalendarEventTypeController;
use App\Http\Controllers\SchoolCalendarController;

use App\Http\Controllers\AdmissionSessionController;
use App\Http\Controllers\AdmissionEnquiryController;
use App\Http\Controllers\AdmissionFollowupController;
use App\Http\Controllers\AdmissionApplicationController;
use App\Http\Controllers\AdmissionApplicationDocumentController;
use App\Http\Controllers\AdmissionApplicationReviewController;

use App\Http\Controllers\StudentAdmissionController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\StudentEnrollmentController;
use App\Http\Controllers\StudentPromotionController;
use App\Http\Controllers\DirectStudentAdmissionController;
use App\Http\Controllers\StudentImportController;
use App\Http\Controllers\StudentDocumentController;
use App\Http\Controllers\StudentAttendanceController;

use App\Http\Controllers\FeeHeadController;
use App\Http\Controllers\FeeStructureController;
use App\Http\Controllers\FeeInstallmentController;
use App\Http\Controllers\StudentFeeAssignmentController;
use App\Http\Controllers\FeeDashboardController;
use App\Http\Controllers\FeeCycleController;
use App\Http\Controllers\FeeComponentGroupController;
use App\Http\Controllers\MiscFeeComponentController;
use App\Http\Controllers\BankMasterController;
use App\Http\Controllers\SchoolAccountController;
use App\Http\Controllers\FeeReceiptSchemeController;
use App\Http\Controllers\LateFeeFineRuleController;
use App\Http\Controllers\ConcessionTypeController;
use App\Http\Controllers\PaymentModeController;
use App\Http\Controllers\ChequeBounceReasonController;
use App\Http\Controllers\FeeCompileController;
use App\Http\Controllers\FeeCompileQueueController;
use App\Http\Controllers\OptionalFeeAssignmentController;
use App\Http\Controllers\ConcessionAssignmentController;
use App\Http\Controllers\FeeWaiverAssignmentController;
use App\Http\Controllers\FineWaiverAssignmentController;
use App\Http\Controllers\CompositeConcessionController;
use App\Http\Controllers\FeeReceiptController;
use App\Http\Controllers\FeeRefundController;
use App\Http\Controllers\FeeChequeDdDetailController;

use App\Http\Controllers\TcReasonController;
use App\Http\Controllers\TcRemarkOptionController;
use App\Http\Controllers\TcLastResultOptionController;
use App\Http\Controllers\PromotionStatusController;
use App\Http\Controllers\StudentManagementController;
use App\Http\Controllers\StudentSuspensionController;
use App\Http\Controllers\StudentDeregistrationController;
use App\Http\Controllers\TransferCertificateController;
use App\Http\Controllers\ManualTransferCertificateController;
use App\Http\Controllers\TcRequestController;
use App\Http\Controllers\StudentProfileModifyRequestController;

/*
|--------------------------------------------------------------------------
| ROOT
|--------------------------------------------------------------------------
*/

Route::get('/', function () {

    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');

});


/*
|--------------------------------------------------------------------------
| GUEST ROUTES
|--------------------------------------------------------------------------
|
| Only users who are NOT logged in can access these.
|
*/

Route::middleware('guest')->group(function () {

    Route::get(
        '/login',
        [LoginController::class, 'showLoginForm']
    )->name('login');


    Route::post(
        '/login',
        [LoginController::class, 'login']
    )->name('login.submit');

});


/*
|--------------------------------------------------------------------------
| AUTHENTICATED SCHOOL ERP
|--------------------------------------------------------------------------
|
| EVERYTHING below requires login.
|
| IMPORTANT:
| auth runs BEFORE permission middleware.
|
*/

Route::middleware(['auth'])->group(function () {


    /*
    |--------------------------------------------------------------------------
    | DASHBOARD / LOGOUT
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/dashboard',
        [DashboardController::class, 'index']
    )->name('dashboard');


    Route::post(
        '/logout',
        [LoginController::class, 'logout']
    )->name('logout');



    /*
    |--------------------------------------------------------------------------
    | ACADEMIC YEAR
    |--------------------------------------------------------------------------
    */

    Route::resource(
        'academic-years',
        AcademicYearController::class
    );



    /*
    |--------------------------------------------------------------------------
    | WINGS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/wings',
        [WingController::class, 'index']
    )
        ->middleware('permission:wing.view')
        ->name('wings.index');


    Route::get(
        '/wings/create',
        [WingController::class, 'create']
    )
        ->middleware('permission:wing.create')
        ->name('wings.create');


    Route::post(
        '/wings',
        [WingController::class, 'store']
    )
        ->middleware('permission:wing.create')
        ->name('wings.store');


    Route::get(
        '/wings/{wing}/edit',
        [WingController::class, 'edit']
    )
        ->middleware('permission:wing.edit')
        ->name('wings.edit');


    Route::put(
        '/wings/{wing}',
        [WingController::class, 'update']
    )
        ->middleware('permission:wing.edit')
        ->name('wings.update');


    Route::delete(
        '/wings/{wing}',
        [WingController::class, 'destroy']
    )
        ->middleware('permission:wing.delete')
        ->name('wings.destroy');



    /*
    |--------------------------------------------------------------------------
    | CLASSES
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/classes',
        [SchoolClassController::class, 'index']
    )
        ->middleware('permission:class.view')
        ->name('school-classes.index');


    Route::get(
        '/classes/create',
        [SchoolClassController::class, 'create']
    )
        ->middleware('permission:class.create')
        ->name('school-classes.create');


    Route::post(
        '/classes',
        [SchoolClassController::class, 'store']
    )
        ->middleware('permission:class.create')
        ->name('school-classes.store');


    Route::get(
        '/classes/{schoolClass}/edit',
        [SchoolClassController::class, 'edit']
    )
        ->middleware('permission:class.edit')
        ->name('school-classes.edit');


    Route::put(
        '/classes/{schoolClass}',
        [SchoolClassController::class, 'update']
    )
        ->middleware('permission:class.edit')
        ->name('school-classes.update');


    Route::delete(
        '/classes/{schoolClass}',
        [SchoolClassController::class, 'destroy']
    )
        ->middleware('permission:class.delete')
        ->name('school-classes.destroy');



    /*
    |--------------------------------------------------------------------------
    | SECTIONS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/sections',
        [SectionController::class, 'index']
    )
        ->middleware('permission:section.view')
        ->name('sections.index');


    Route::get(
        '/sections/create',
        [SectionController::class, 'create']
    )
        ->middleware('permission:section.create')
        ->name('sections.create');


    Route::post(
        '/sections',
        [SectionController::class, 'store']
    )
        ->middleware('permission:section.create')
        ->name('sections.store');


    Route::get(
        '/sections/{section}/edit',
        [SectionController::class, 'edit']
    )
        ->middleware('permission:section.edit')
        ->name('sections.edit');


    Route::put(
        '/sections/{section}',
        [SectionController::class, 'update']
    )
        ->middleware('permission:section.edit')
        ->name('sections.update');


    Route::delete(
        '/sections/{section}',
        [SectionController::class, 'destroy']
    )
        ->middleware('permission:section.delete')
        ->name('sections.destroy');



    /*
    |--------------------------------------------------------------------------
    | SUBJECTS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/subjects',
        [SubjectController::class, 'index']
    )
        ->middleware('permission:subject.view')
        ->name('subjects.index');


    Route::get(
        '/subjects/create',
        [SubjectController::class, 'create']
    )
        ->middleware('permission:subject.create')
        ->name('subjects.create');


    Route::post(
        '/subjects',
        [SubjectController::class, 'store']
    )
        ->middleware('permission:subject.create')
        ->name('subjects.store');


    Route::get(
        '/subjects/{subject}/edit',
        [SubjectController::class, 'edit']
    )
        ->middleware('permission:subject.edit')
        ->name('subjects.edit');


    Route::put(
        '/subjects/{subject}',
        [SubjectController::class, 'update']
    )
        ->middleware('permission:subject.edit')
        ->name('subjects.update');


    Route::delete(
        '/subjects/{subject}',
        [SubjectController::class, 'destroy']
    )
        ->middleware('permission:subject.delete')
        ->name('subjects.destroy');



    /*
    |--------------------------------------------------------------------------
    | CLASS SUBJECT MAPPING
    |--------------------------------------------------------------------------
    */

    // Static route FIRST

    Route::get(
        '/class-subjects/mapped-subjects',
        [ClassSubjectController::class, 'mappedSubjects']
    )
        ->middleware('permission:class-subject.view')
        ->name('class-subjects.mapped-subjects');


    Route::get(
        '/class-subjects',
        [ClassSubjectController::class, 'index']
    )
        ->middleware('permission:class-subject.view')
        ->name('class-subjects.index');


    Route::get(
        '/class-subjects/create',
        [ClassSubjectController::class, 'create']
    )
        ->middleware('permission:class-subject.create')
        ->name('class-subjects.create');


    Route::post(
        '/class-subjects',
        [ClassSubjectController::class, 'store']
    )
        ->middleware('permission:class-subject.create')
        ->name('class-subjects.store');


    Route::delete(
        '/class-subjects/{classSubject}',
        [ClassSubjectController::class, 'destroy']
    )
        ->whereNumber('classSubject')
        ->middleware('permission:class-subject.delete')
        ->name('class-subjects.destroy');



    /*
    |--------------------------------------------------------------------------
    | SECTION GROUPS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/section-groups/sections',
        [SectionGroupController::class, 'getSections']
    )
        ->middleware('permission:section-group.view')
        ->name('section-groups.sections');


    Route::get(
        '/section-groups',
        [SectionGroupController::class, 'index']
    )
        ->middleware('permission:section-group.view')
        ->name('section-groups.index');


    Route::get(
        '/section-groups/create',
        [SectionGroupController::class, 'create']
    )
        ->middleware('permission:section-group.create')
        ->name('section-groups.create');


    Route::post(
        '/section-groups',
        [SectionGroupController::class, 'store']
    )
        ->middleware('permission:section-group.create')
        ->name('section-groups.store');


    Route::get(
        '/section-groups/{sectionGroup}/edit',
        [SectionGroupController::class, 'edit']
    )
        ->whereNumber('sectionGroup')
        ->middleware('permission:section-group.edit')
        ->name('section-groups.edit');


    Route::put(
        '/section-groups/{sectionGroup}',
        [SectionGroupController::class, 'update']
    )
        ->whereNumber('sectionGroup')
        ->middleware('permission:section-group.edit')
        ->name('section-groups.update');


    Route::delete(
        '/section-groups/{sectionGroup}',
        [SectionGroupController::class, 'destroy']
    )
        ->whereNumber('sectionGroup')
        ->middleware('permission:section-group.delete')
        ->name('section-groups.destroy');



    /*
    |--------------------------------------------------------------------------
    | CALENDAR EVENT TYPES
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/calendar-event-types',
        [CalendarEventTypeController::class, 'index']
    )
        ->middleware('permission:calendar.view')
        ->name('calendar-event-types.index');


    Route::get(
        '/calendar-event-types/create',
        [CalendarEventTypeController::class, 'create']
    )
        ->middleware('permission:calendar.create')
        ->name('calendar-event-types.create');


    Route::post(
        '/calendar-event-types',
        [CalendarEventTypeController::class, 'store']
    )
        ->middleware('permission:calendar.create')
        ->name('calendar-event-types.store');


    Route::get(
        '/calendar-event-types/{calendarEventType}/edit',
        [CalendarEventTypeController::class, 'edit']
    )
        ->whereNumber('calendarEventType')
        ->middleware('permission:calendar.edit')
        ->name('calendar-event-types.edit');


    Route::put(
        '/calendar-event-types/{calendarEventType}',
        [CalendarEventTypeController::class, 'update']
    )
        ->whereNumber('calendarEventType')
        ->middleware('permission:calendar.edit')
        ->name('calendar-event-types.update');


    Route::delete(
        '/calendar-event-types/{calendarEventType}',
        [CalendarEventTypeController::class, 'destroy']
    )
        ->whereNumber('calendarEventType')
        ->middleware('permission:calendar.delete')
        ->name('calendar-event-types.destroy');



    /*
    |--------------------------------------------------------------------------
    | SCHOOL CALENDAR
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/school-calendar',
        [SchoolCalendarController::class, 'index']
    )
        ->middleware('permission:calendar.view')
        ->name('school-calendar.index');


    Route::get(
        '/school-calendar/create',
        [SchoolCalendarController::class, 'create']
    )
        ->middleware('permission:calendar.create')
        ->name('school-calendar.create');


    Route::post(
        '/school-calendar',
        [SchoolCalendarController::class, 'store']
    )
        ->middleware('permission:calendar.create')
        ->name('school-calendar.store');


    Route::get(
        '/school-calendar/{schoolCalendarEvent}/edit',
        [SchoolCalendarController::class, 'edit']
    )
        ->whereNumber('schoolCalendarEvent')
        ->middleware('permission:calendar.edit')
        ->name('school-calendar.edit');


    Route::put(
        '/school-calendar/{schoolCalendarEvent}',
        [SchoolCalendarController::class, 'update']
    )
        ->whereNumber('schoolCalendarEvent')
        ->middleware('permission:calendar.edit')
        ->name('school-calendar.update');


    Route::delete(
        '/school-calendar/{schoolCalendarEvent}',
        [SchoolCalendarController::class, 'destroy']
    )
        ->whereNumber('schoolCalendarEvent')
        ->middleware('permission:calendar.delete')
        ->name('school-calendar.destroy');



    /*
    |--------------------------------------------------------------------------
    | ADMISSION SESSION
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admission-sessions',
        [AdmissionSessionController::class, 'index']
    )
        ->middleware('permission:admission-session.view')
        ->name('admission-sessions.index');


    Route::get(
        '/admission-sessions/create',
        [AdmissionSessionController::class, 'create']
    )
        ->middleware('permission:admission-session.create')
        ->name('admission-sessions.create');


    Route::post(
        '/admission-sessions',
        [AdmissionSessionController::class, 'store']
    )
        ->middleware('permission:admission-session.create')
        ->name('admission-sessions.store');


    Route::get(
        '/admission-sessions/{admissionSession}/edit',
        [AdmissionSessionController::class, 'edit']
    )
        ->whereNumber('admissionSession')
        ->middleware('permission:admission-session.edit')
        ->name('admission-sessions.edit');


    Route::put(
        '/admission-sessions/{admissionSession}',
        [AdmissionSessionController::class, 'update']
    )
        ->whereNumber('admissionSession')
        ->middleware('permission:admission-session.edit')
        ->name('admission-sessions.update');


    Route::delete(
        '/admission-sessions/{admissionSession}',
        [AdmissionSessionController::class, 'destroy']
    )
        ->whereNumber('admissionSession')
        ->middleware('permission:admission-session.delete')
        ->name('admission-sessions.destroy');



    /*
    |--------------------------------------------------------------------------
    | ADMISSION ENQUIRIES
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admission-enquiries',
        [AdmissionEnquiryController::class, 'index']
    )
        ->middleware('permission:admission-enquiry.view')
        ->name('admission-enquiries.index');


    Route::get(
        '/admission-enquiries/create',
        [AdmissionEnquiryController::class, 'create']
    )
        ->middleware('permission:admission-enquiry.create')
        ->name('admission-enquiries.create');


    Route::post(
        '/admission-enquiries',
        [AdmissionEnquiryController::class, 'store']
    )
        ->middleware('permission:admission-enquiry.create')
        ->name('admission-enquiries.store');


    Route::get(
        '/admission-enquiries/{admissionEnquiry}/edit',
        [AdmissionEnquiryController::class, 'edit']
    )
        ->whereNumber('admissionEnquiry')
        ->middleware('permission:admission-enquiry.edit')
        ->name('admission-enquiries.edit');


    Route::put(
        '/admission-enquiries/{admissionEnquiry}',
        [AdmissionEnquiryController::class, 'update']
    )
        ->whereNumber('admissionEnquiry')
        ->middleware('permission:admission-enquiry.edit')
        ->name('admission-enquiries.update');


    Route::delete(
        '/admission-enquiries/{admissionEnquiry}',
        [AdmissionEnquiryController::class, 'destroy']
    )
        ->whereNumber('admissionEnquiry')
        ->middleware('permission:admission-enquiry.delete')
        ->name('admission-enquiries.destroy');



    /*
    |--------------------------------------------------------------------------
    | ADMISSION FOLLOWUPS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admission-followups',
        [AdmissionFollowupController::class, 'index']
    )
        ->middleware('permission:admission-followup.view')
        ->name('admission-followups.index');


    Route::get(
        '/admission-enquiries/{admissionEnquiry}/followups/create',
        [AdmissionFollowupController::class, 'create']
    )
        ->whereNumber('admissionEnquiry')
        ->middleware('permission:admission-followup.create')
        ->name('admission-followups.create');


    Route::post(
        '/admission-enquiries/{admissionEnquiry}/followups',
        [AdmissionFollowupController::class, 'store']
    )
        ->whereNumber('admissionEnquiry')
        ->middleware('permission:admission-followup.create')
        ->name('admission-followups.store');


    Route::get(
        '/admission-followups/{admissionFollowup}/edit',
        [AdmissionFollowupController::class, 'edit']
    )
        ->whereNumber('admissionFollowup')
        ->middleware('permission:admission-followup.edit')
        ->name('admission-followups.edit');


    Route::put(
        '/admission-followups/{admissionFollowup}',
        [AdmissionFollowupController::class, 'update']
    )
        ->whereNumber('admissionFollowup')
        ->middleware('permission:admission-followup.edit')
        ->name('admission-followups.update');


    Route::delete(
        '/admission-followups/{admissionFollowup}',
        [AdmissionFollowupController::class, 'destroy']
    )
        ->whereNumber('admissionFollowup')
        ->middleware('permission:admission-followup.delete')
        ->name('admission-followups.destroy');



    /*
    |--------------------------------------------------------------------------
    | ADMISSION APPLICATIONS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admission-applications',
        [AdmissionApplicationController::class, 'index']
    )
        ->middleware('permission:admission-application.view')
        ->name('admission-applications.index');


    Route::get(
        '/admission-enquiries/{admissionEnquiry}/application/create',
        [AdmissionApplicationController::class, 'create']
    )
        ->whereNumber('admissionEnquiry')
        ->middleware('permission:admission-application.create')
        ->name('admission-applications.create');


    Route::post(
        '/admission-enquiries/{admissionEnquiry}/application',
        [AdmissionApplicationController::class, 'store']
    )
        ->whereNumber('admissionEnquiry')
        ->middleware('permission:admission-application.create')
        ->name('admission-applications.store');


    Route::get(
        '/admission-applications/{admissionApplication}/edit',
        [AdmissionApplicationController::class, 'edit']
    )
        ->whereNumber('admissionApplication')
        ->middleware('permission:admission-application.edit')
        ->name('admission-applications.edit');


    Route::put(
        '/admission-applications/{admissionApplication}',
        [AdmissionApplicationController::class, 'update']
    )
        ->whereNumber('admissionApplication')
        ->middleware('permission:admission-application.edit')
        ->name('admission-applications.update');



    /*
    |--------------------------------------------------------------------------
    | ADMISSION DOCUMENTS
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/admission-applications/{admissionApplication}/documents',
        [AdmissionApplicationDocumentController::class, 'store']
    )
        ->whereNumber('admissionApplication')
        ->middleware('permission:admission-document.create')
        ->name('admission-documents.store');


    Route::put(
        '/admission-documents/{document}/verify',
        [AdmissionApplicationDocumentController::class, 'verify']
    )
        ->whereNumber('document')
        ->middleware('permission:admission-document.verify')
        ->name('admission-documents.verify');


    Route::delete(
        '/admission-documents/{document}',
        [AdmissionApplicationDocumentController::class, 'destroy']
    )
        ->whereNumber('document')
        ->middleware('permission:admission-document.delete')
        ->name('admission-documents.destroy');



    /*
    |--------------------------------------------------------------------------
    | APPLICATION REVIEW
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/admission-applications/{admissionApplication}/review',
        [AdmissionApplicationReviewController::class, 'review']
    )
        ->whereNumber('admissionApplication')
        ->middleware('permission:admission-application.review')
        ->name('admission-applications.review');


    Route::post(
        '/admission-applications/{admissionApplication}/approve',
        [AdmissionApplicationReviewController::class, 'approve']
    )
        ->whereNumber('admissionApplication')
        ->middleware('permission:admission-application.approve')
        ->name('admission-applications.approve');


    Route::post(
        '/admission-applications/{admissionApplication}/reject',
        [AdmissionApplicationReviewController::class, 'reject']
    )
        ->whereNumber('admissionApplication')
        ->middleware('permission:admission-application.reject')
        ->name('admission-applications.reject');


    /*
    |--------------------------------------------------------------------------
    | APPLICATION -> STUDENT
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/admission-applications/{admissionApplication}/admit',
        [StudentAdmissionController::class, 'create']
    )
        ->whereNumber('admissionApplication')
        ->middleware('permission:student-admission.create')
        ->name('student-admissions.create');


    Route::post(
        '/admission-applications/{admissionApplication}/admit',
        [StudentAdmissionController::class, 'store']
    )
        ->whereNumber('admissionApplication')
        ->middleware('permission:student-admission.create')
        ->name('student-admissions.store');



    /*
    |--------------------------------------------------------------------------
    | STUDENTS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/students',
        [StudentController::class, 'index']
    )
        ->middleware('permission:student.view')
        ->name('students.index');

    Route::get(
        '/student-management/get-sections',
        [StudentManagementController::class, 'getSections']
    )->name('student-management.get-sections');    

    /*
    |--------------------------------------------------------------------------
    | DIRECT STUDENT ADMISSION
    |--------------------------------------------------------------------------
    |
    | Static student routes MUST stay before /students/{student}
    |
    */

    Route::get(
        '/students/direct-admission/sections',
        [DirectStudentAdmissionController::class, 'getSections']
    )
        ->middleware('permission:student.create')
        ->name('students.direct-admission.sections');


    Route::get(
        '/students/create',
        [DirectStudentAdmissionController::class, 'create']
    )
        ->middleware('permission:student.create')
        ->name('students.create');


    Route::post(
        '/students',
        [DirectStudentAdmissionController::class, 'store']
    )
        ->middleware('permission:student.create')
        ->name('students.store');



    /*
    |--------------------------------------------------------------------------
    | STUDENT BULK IMPORT
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/students/import',
        [StudentImportController::class, 'index']
    )
        ->middleware('permission:student.create')
        ->name('students.import');


    Route::get(
        '/students/import/template',
        [StudentImportController::class, 'downloadTemplate']
    )
        ->middleware('permission:student.create')
        ->name('students.import.template');


    Route::post(
        '/students/import/preview',
        [StudentImportController::class, 'preview']
    )
        ->middleware('permission:student.create')
        ->name('students.import.preview');


    Route::post(
        '/students/import/confirm',
        [StudentImportController::class, 'confirm']
    )
        ->middleware('permission:student.create')
        ->name('students.import.confirm');



    /*
    |--------------------------------------------------------------------------
    | STUDENT DOCUMENTS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/student-documents',
        [StudentDocumentController::class, 'index']
    )
        ->middleware('permission:student-document.view')
        ->name('student-documents.index');


    Route::get(
        '/student-documents/create',
        [StudentDocumentController::class, 'create']
    )
        ->middleware('permission:student-document.create')
        ->name('student-documents.create');


    Route::get(
        '/students/{student}/documents',
        [StudentDocumentController::class, 'studentDocuments']
    )
        ->whereNumber('student')
        ->middleware('permission:student-document.view')
        ->name('student-documents.student');


    Route::post(
        '/students/{student}/documents',
        [StudentDocumentController::class, 'store']
    )
        ->whereNumber('student')
        ->middleware('permission:student-document.create')
        ->name('student-documents.store');


    Route::get(
        '/student-documents/{studentDocument}/edit',
        [StudentDocumentController::class, 'edit']
    )
        ->whereNumber('studentDocument')
        ->middleware('permission:student-document.edit')
        ->name('student-documents.edit');


    Route::put(
        '/student-documents/{studentDocument}',
        [StudentDocumentController::class, 'update']
    )
        ->whereNumber('studentDocument')
        ->middleware('permission:student-document.edit')
        ->name('student-documents.update');


    Route::delete(
        '/student-documents/{studentDocument}',
        [StudentDocumentController::class, 'destroy']
    )
        ->whereNumber('studentDocument')
        ->middleware('permission:student-document.delete')
        ->name('student-documents.destroy');

    Route::get(
        '/student-documents/get-sections',
        [StudentDocumentController::class, 'getSections']
    )->name('student-documents.get-sections');

    /*
    |--------------------------------------------------------------------------
    | STUDENT ENROLLMENTS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/student-enrollments/sections',
        [StudentEnrollmentController::class, 'getSections']
    )
        ->middleware('permission:student-enrollment.view')
        ->name('student-enrollments.sections');


    Route::get(
        '/students/{student}/enrollments/create',
        [StudentEnrollmentController::class, 'create']
    )
        ->whereNumber('student')
        ->middleware('permission:student-enrollment.create')
        ->name('student-enrollments.create');


    Route::post(
        '/students/{student}/enrollments',
        [StudentEnrollmentController::class, 'store']
    )
        ->whereNumber('student')
        ->middleware('permission:student-enrollment.create')
        ->name('student-enrollments.store');


    Route::get(
        '/student-enrollments/{studentEnrollment}/edit',
        [StudentEnrollmentController::class, 'edit']
    )
        ->whereNumber('studentEnrollment')
        ->middleware('permission:student-enrollment.edit')
        ->name('student-enrollments.edit');


    Route::put(
        '/student-enrollments/{studentEnrollment}',
        [StudentEnrollmentController::class, 'update']
    )
        ->whereNumber('studentEnrollment')
        ->middleware('permission:student-enrollment.edit')
        ->name('student-enrollments.update');



    /*
    |--------------------------------------------------------------------------
    | Student Promotions / Repetitions
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/student-promotions',
        [StudentPromotionController::class, 'index']
    )->name('student-promotions.index');


    Route::get(
        '/student-promotions/get-sections',
        [StudentPromotionController::class, 'getSections']
    )->name('student-promotions.get-sections');


    Route::get(
        '/student-promotions/get-students',
        [StudentPromotionController::class, 'getStudents']
    )->name('student-promotions.get-students');


    Route::post(
        '/student-promotions',
        [StudentPromotionController::class, 'store']
    )->name('student-promotions.store');



    /*
    |--------------------------------------------------------------------------
    | INDIVIDUAL STUDENT
    |--------------------------------------------------------------------------
    |
    | Keep these AFTER static /students/... routes.
    |
    */

    Route::get(
        '/students/{student}',
        [StudentController::class, 'show']
    )
        ->whereNumber('student')
        ->middleware('permission:student.view')
        ->name('students.show');


    Route::get(
        '/students/{student}/edit',
        [StudentController::class, 'edit']
    )
        ->whereNumber('student')
        ->middleware('permission:student.edit')
        ->name('students.edit');


    Route::put(
        '/students/{student}',
        [StudentController::class, 'update']
    )
        ->whereNumber('student')
        ->middleware('permission:student.edit')
        ->name('students.update');

    Route::get(
        '/student-documents/sections',
        [StudentDocumentController::class, 'getSections']
    )
        ->middleware('permission:student-document.create')
        ->name('student-documents.sections');


    Route::get(
        '/student-documents/students',
        [StudentDocumentController::class, 'getStudents']
    )
        ->middleware('permission:student-document.create')
        ->name('student-documents.students');    

    Route::get(
        '/students/editable-list',
        [StudentController::class, 'editableList']
    )->name('students.editable-list');

    Route::put(
        '/students/editable-list/{student}',
        [StudentController::class, 'updateEditableList']
    )->name('students.editable-list.update');    

    /*
    |--------------------------------------------------------------------------
    | STUDENT ATTENDANCE
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/student-attendance',
        [StudentAttendanceController::class, 'create']
    )
        ->middleware('permission:student-attendance.create')
        ->name('student-attendance.create');


    Route::get(
        '/student-attendance/sections',
        [StudentAttendanceController::class, 'getSections']
    )
        ->middleware('permission:student-attendance.create')
        ->name('student-attendance.sections');


    Route::get(
        '/student-attendance/students',
        [StudentAttendanceController::class, 'getStudents']
    )
        ->middleware('permission:student-attendance.create')
        ->name('student-attendance.students');


    Route::post(
        '/student-attendance',
        [StudentAttendanceController::class, 'store']
    )
        ->middleware('permission:student-attendance.create')
        ->name('student-attendance.store');   

    Route::get(
        '/students/update-images',
        [StudentController::class, 'studentImages']
    )->name('students.images');

    Route::put(
        '/students/update-images/{student}',
        [StudentController::class, 'updateStudentImage']
    )->name('students.images.update');   
    
    Route::get(
        '/students/update-images',
        [StudentController::class, 'studentImages']
    )->name('students.images');


    Route::post(
        '/students/update-images/{student}',
        [StudentController::class, 'uploadStudentImages']
    )->name('students.images.upload');


    Route::delete(
        '/students/update-images/{student}/{imageType}',
        [StudentController::class, 'deleteStudentImage']
    )->name('students.images.delete');

    Route::get(
        '/student-management/assign-roll-no',
        [StudentManagementController::class, 'assignRollNo']
    )->name('student-management.assign-roll-no');

    Route::post(
        '/student-management/assign-roll-no',
        [StudentManagementController::class, 'updateRollNo']
    )->name('student-management.assign-roll-no.update');

    Route::get(
        '/student-management/section-change',
        [StudentManagementController::class, 'sectionChange']
    )->name('student-management.section-change');

    Route::post(
        '/student-management/section-change',
        [StudentManagementController::class, 'updateSection']
    )->name('student-management.section-change.update');

    Route::get(
        '/student-management/section-change-multiple',
        [StudentManagementController::class, 'sectionChangeMultiple']
    )->name('student-management.section-change-multiple');

    Route::post(
        '/student-management/section-change-multiple',
        [StudentManagementController::class, 'updateSectionMultiple']
    )->name('student-management.section-change-multiple.update');

    Route::get(
        '/student-management/class-change',
        [StudentManagementController::class, 'classChange']
    )->name('student-management.class-change');

    Route::post(
        '/student-management/class-change',
        [StudentManagementController::class, 'updateClass']
    )->name('student-management.class-change.update');

    Route::get(
        '/student-management/type-change',
        [StudentManagementController::class, 'typeChange']
    )->name('student-management.type-change');

    Route::post(
        '/student-management/type-change',
        [StudentManagementController::class, 'updateType']
    )->name('student-management.type-change.update');

    Route::get(
        '/student-management/suspension',
        [StudentSuspensionController::class, 'index']
    )->name('student-management.suspension');


    Route::post(
        '/student-management/suspension',
        [StudentSuspensionController::class, 'store']
    )->name('student-management.suspension.store');


    Route::post(
        '/student-management/suspension/{suspension}/revoke',
        [StudentSuspensionController::class, 'revoke']
    )->name('student-management.suspension.revoke');

    Route::get(
        '/student-management/de-registration',
        [StudentDeregistrationController::class, 'index']
    )->name('student-management.deregistration');


    Route::post(
        '/student-management/de-registration',
        [StudentDeregistrationController::class, 'store']
    )->name('student-management.deregistration.store');

    Route::get(
        '/student-management/transfer-certificate',
        [TransferCertificateController::class, 'index']
    )->name('student-management.transfer-certificate');


    Route::post(
        '/student-management/transfer-certificate',
        [TransferCertificateController::class, 'store']
    )->name('student-management.transfer-certificate.store');


    Route::post(
        '/student-management/transfer-certificate/{certificate}/issue',
        [TransferCertificateController::class, 'issue']
    )->name('student-management.transfer-certificate.issue');

    Route::post(
        '/student-management/transfer-certificate/{certificate}/cancel',
        [TransferCertificateController::class, 'cancel']
    )->name('student-management.transfer-certificate.cancel');

    Route::get(
        '/student-management/manual-transfer-certificate',
        [ManualTransferCertificateController::class, 'index']
    )->name('student-management.manual-transfer-certificate');


    Route::get(
        '/student-management/manual-transfer-certificate/create',
        [ManualTransferCertificateController::class, 'create']
    )->name('student-management.manual-transfer-certificate.create');


    Route::post(
        '/student-management/manual-transfer-certificate',
        [ManualTransferCertificateController::class, 'store']
    )->name('student-management.manual-transfer-certificate.store');


    Route::post(
        '/student-management/manual-transfer-certificate/{certificate}/issue',
        [ManualTransferCertificateController::class, 'issue']
    )->name('student-management.manual-transfer-certificate.issue');


    Route::post(
        '/student-management/manual-transfer-certificate/{certificate}/cancel',
        [ManualTransferCertificateController::class, 'cancel']
    )->name('student-management.manual-transfer-certificate.cancel');
        
    /*
    |--------------------------------------------------------------------------
    | FEE HEADS
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/fee-heads',
        [FeeHeadController::class, 'index']
    )
        ->middleware('permission:fee-head.view')
        ->name('fee-heads.index');


    Route::get(
        '/fee-heads/create',
        [FeeHeadController::class, 'create']
    )
        ->middleware('permission:fee-head.create')
        ->name('fee-heads.create');


    Route::post(
        '/fee-heads',
        [FeeHeadController::class, 'store']
    )
        ->middleware('permission:fee-head.create')
        ->name('fee-heads.store');


    Route::get(
        '/fee-heads/{feeHead}/edit',
        [FeeHeadController::class, 'edit']
    )
        ->whereNumber('feeHead')
        ->middleware('permission:fee-head.edit')
        ->name('fee-heads.edit');


    Route::put(
        '/fee-heads/{feeHead}',
        [FeeHeadController::class, 'update']
    )
        ->whereNumber('feeHead')
        ->middleware('permission:fee-head.edit')
        ->name('fee-heads.update');


    Route::delete(
        '/fee-heads/{feeHead}',
        [FeeHeadController::class, 'destroy']
    )
        ->whereNumber('feeHead')
        ->middleware('permission:fee-head.delete')
        ->name('fee-heads.destroy');    

    Route::get(
        '/student-management/tc-requests',
        [TcRequestController::class, 'index']
    )->name('student-management.tc-requests');

    Route::post(
        '/student-management/tc-requests',
        [TcRequestController::class, 'store']
    )->name('student-management.tc-requests.store');

    Route::post(
        '/student-management/tc-requests/{tcRequest}/approve',
        [TcRequestController::class, 'approve']
    )->name('student-management.tc-requests.approve');

    Route::post(
        '/student-management/tc-requests/{tcRequest}/reject',
        [TcRequestController::class, 'reject']
    )->name('student-management.tc-requests.reject');    

    Route::get(
        '/student-management/profile-modify-requests',
        [StudentProfileModifyRequestController::class, 'index']
    )->name('student-management.profile-modify-requests');

    Route::post(
        '/student-management/profile-modify-requests',
        [StudentProfileModifyRequestController::class, 'store']
    )->name('student-management.profile-modify-requests.store');

    Route::post(
        '/student-management/profile-modify-requests/{modifyRequest}/approve',
        [StudentProfileModifyRequestController::class, 'approve']
    )->name('student-management.profile-modify-requests.approve');

    Route::post(
        '/student-management/profile-modify-requests/{modifyRequest}/reject',
        [StudentProfileModifyRequestController::class, 'reject']
    )->name('student-management.profile-modify-requests.reject');

    /*
    |--------------------------------------------------------------------------
    | FEE STRUCTURES
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/fee-structures',
        [FeeStructureController::class, 'index']
    )
        ->middleware('permission:fee-structure.view')
        ->name('fee-structures.index');


    Route::get(
        '/fee-structures/create',
        [FeeStructureController::class, 'create']
    )
        ->middleware('permission:fee-structure.create')
        ->name('fee-structures.create');


    Route::post(
        '/fee-structures',
        [FeeStructureController::class, 'store']
    )
        ->middleware('permission:fee-structure.create')
        ->name('fee-structures.store');


    Route::get(
        '/fee-structures/{feeStructure}/edit',
        [FeeStructureController::class, 'edit']
    )
        ->whereNumber('feeStructure')
        ->middleware('permission:fee-structure.edit')
        ->name('fee-structures.edit');


    Route::put(
        '/fee-structures/{feeStructure}',
        [FeeStructureController::class, 'update']
    )
        ->whereNumber('feeStructure')
        ->middleware('permission:fee-structure.edit')
        ->name('fee-structures.update');


    Route::delete(
        '/fee-structures/{feeStructure}',
        [FeeStructureController::class, 'destroy']
    )
        ->whereNumber('feeStructure')
        ->middleware('permission:fee-structure.delete')
        ->name('fee-structures.destroy');    

    /*
    |--------------------------------------------------------------------------
    | FEE INSTALLMENTS / SCHEDULE
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/fee-structures/{feeStructure}/installments',
        [FeeInstallmentController::class, 'index']
    )
        ->whereNumber('feeStructure')
        ->middleware('permission:fee-installment.view')
        ->name('fee-installments.index');


    Route::get(
        '/fee-structures/{feeStructure}/items/{feeStructureItem}/installments/create',
        [FeeInstallmentController::class, 'create']
    )
        ->whereNumber([
            'feeStructure',
            'feeStructureItem'
        ])
        ->middleware('permission:fee-installment.create')
        ->name('fee-installments.create');


    Route::post(
        '/fee-structures/{feeStructure}/items/{feeStructureItem}/installments',
        [FeeInstallmentController::class, 'store']
    )
        ->whereNumber([
            'feeStructure',
            'feeStructureItem'
        ])
        ->middleware('permission:fee-installment.create')
        ->name('fee-installments.store');


    Route::post(
        '/fee-structures/{feeStructure}/items/{feeStructureItem}/installments/generate',
        [FeeInstallmentController::class, 'generate']
    )
        ->whereNumber([
            'feeStructure',
            'feeStructureItem'
        ])
        ->middleware('permission:fee-installment.create')
        ->name('fee-installments.generate');


    Route::get(
        '/fee-installments/{feeInstallment}/edit',
        [FeeInstallmentController::class, 'edit']
    )
        ->whereNumber('feeInstallment')
        ->middleware('permission:fee-installment.edit')
        ->name('fee-installments.edit');


    Route::put(
        '/fee-installments/{feeInstallment}',
        [FeeInstallmentController::class, 'update']
    )
        ->whereNumber('feeInstallment')
        ->middleware('permission:fee-installment.edit')
        ->name('fee-installments.update');


    Route::delete(
        '/fee-installments/{feeInstallment}',
        [FeeInstallmentController::class, 'destroy']
    )
        ->whereNumber('feeInstallment')
        ->middleware('permission:fee-installment.delete')
        ->name('fee-installments.destroy');

    /*
    |--------------------------------------------------------------------------
    | Student Fee Assignment
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/student-fee-assignments',
        [StudentFeeAssignmentController::class, 'index']
    )
        ->middleware('permission:student-fee-assignment.view')
        ->name('student-fee-assignments.index');


    Route::get(
        '/student-fee-assignments/create',
        [StudentFeeAssignmentController::class, 'create']
    )
        ->middleware('permission:student-fee-assignment.create')
        ->name('student-fee-assignments.create');


    Route::get(
        '/student-fee-assignments/sections',
        [StudentFeeAssignmentController::class, 'getSections']
    )
        ->middleware('permission:student-fee-assignment.create')
        ->name('student-fee-assignments.sections');


    Route::get(
        '/student-fee-assignments/students',
        [StudentFeeAssignmentController::class, 'getStudents']
    )
        ->middleware('permission:student-fee-assignment.create')
        ->name('student-fee-assignments.students');


    Route::get(
        '/student-fee-assignments/fee-structures',
        [StudentFeeAssignmentController::class, 'getFeeStructures']
    )
        ->middleware('permission:student-fee-assignment.create')
        ->name('student-fee-assignments.fee-structures');


    Route::post(
        '/student-fee-assignments/preview',
        [StudentFeeAssignmentController::class, 'preview']
    )
        ->middleware('permission:student-fee-assignment.create')
        ->name('student-fee-assignments.preview');


    Route::post(
        '/student-fee-assignments',
        [StudentFeeAssignmentController::class, 'store']
    )
        ->middleware('permission:student-fee-assignment.create')
        ->name('student-fee-assignments.store');


    Route::get(
        '/student-fee-assignments/{studentFeeAssignment}',
        [StudentFeeAssignmentController::class, 'show']
    )
        ->whereNumber('studentFeeAssignment')
        ->middleware('permission:student-fee-assignment.view')
        ->name('student-fee-assignments.show');

    Route::get(
        '/student-fee-assignments/bulk',
        [StudentFeeAssignmentController::class, 'bulkCreate']
    )
        ->middleware('permission:student-fee-assignment.bulk-create')
        ->name('student-fee-assignments.bulk');


    Route::post(
        '/student-fee-assignments/bulk',
        [StudentFeeAssignmentController::class, 'bulkStore']
    )
        ->middleware('permission:student-fee-assignment.bulk-create')
        ->name('student-fee-assignments.bulk.store');    

    Route::get(
        '/fees',
        [FeeDashboardController::class, 'index']
    )
        ->middleware('permission:fee-head.view')
        ->name('fees.dashboard');    

    Route::get(
        '/fees/setup/cycles',
        [FeeCycleController::class, 'index']
    )
        ->middleware('permission:fee-cycle.view')
        ->name('fee-cycles.index');


    Route::get(
        '/fees/setup/cycles/create',
        [FeeCycleController::class, 'create']
    )
        ->middleware('permission:fee-cycle.create')
        ->name('fee-cycles.create');


    Route::post(
        '/fees/setup/cycles',
        [FeeCycleController::class, 'store']
    )
        ->middleware('permission:fee-cycle.create')
        ->name('fee-cycles.store');


    Route::get(
        '/fees/setup/cycles/{feeCycle}/edit',
        [FeeCycleController::class, 'edit']
    )
        ->whereNumber('feeCycle')
        ->middleware('permission:fee-cycle.edit')
        ->name('fee-cycles.edit');


    Route::put(
        '/fees/setup/cycles/{feeCycle}',
        [FeeCycleController::class, 'update']
    )
        ->whereNumber('feeCycle')
        ->middleware('permission:fee-cycle.edit')
        ->name('fee-cycles.update');


    Route::delete(
        '/fees/setup/cycles/{feeCycle}',
        [FeeCycleController::class, 'destroy']
    )
        ->whereNumber('feeCycle')
        ->middleware('permission:fee-cycle.delete')
        ->name('fee-cycles.destroy');    

    Route::get(
        '/fees/setup/component-groups',
        [FeeComponentGroupController::class, 'index']
    )
        ->middleware('permission:fee-component-group.view')
        ->name('fee-component-groups.index');


    Route::get(
        '/fees/setup/component-groups/create',
        [FeeComponentGroupController::class, 'create']
    )
        ->middleware('permission:fee-component-group.create')
        ->name('fee-component-groups.create');


    Route::post(
        '/fees/setup/component-groups',
        [FeeComponentGroupController::class, 'store']
    )
        ->middleware('permission:fee-component-group.create')
        ->name('fee-component-groups.store');


    Route::get(
        '/fees/setup/component-groups/{feeComponentGroup}/edit',
        [FeeComponentGroupController::class, 'edit']
    )
        ->whereNumber('feeComponentGroup')
        ->middleware('permission:fee-component-group.edit')
        ->name('fee-component-groups.edit');


    Route::put(
        '/fees/setup/component-groups/{feeComponentGroup}',
        [FeeComponentGroupController::class, 'update']
    )
        ->whereNumber('feeComponentGroup')
        ->middleware('permission:fee-component-group.edit')
        ->name('fee-component-groups.update');


    Route::delete(
        '/fees/setup/component-groups/{feeComponentGroup}',
        [FeeComponentGroupController::class, 'destroy']
    )
        ->whereNumber('feeComponentGroup')
        ->middleware('permission:fee-component-group.delete')
        ->name('fee-component-groups.destroy');    

    Route::get(
        '/fees/setup/misc-components',
        [MiscFeeComponentController::class, 'index']
    )
        ->middleware(
            'permission:misc-fee-component.view'
        )
        ->name('misc-fee-components.index');


    Route::get(
        '/fees/setup/misc-components/create',
        [MiscFeeComponentController::class, 'create']
    )
        ->middleware(
            'permission:misc-fee-component.create'
        )
        ->name('misc-fee-components.create');


    Route::post(
        '/fees/setup/misc-components',
        [MiscFeeComponentController::class, 'store']
    )
        ->middleware(
            'permission:misc-fee-component.create'
        )
        ->name('misc-fee-components.store');


    Route::get(
        '/fees/setup/misc-components/{miscFeeComponent}/edit',
        [MiscFeeComponentController::class, 'edit']
    )
        ->whereNumber('miscFeeComponent')
        ->middleware(
            'permission:misc-fee-component.edit'
        )
        ->name('misc-fee-components.edit');


    Route::put(
        '/fees/setup/misc-components/{miscFeeComponent}',
        [MiscFeeComponentController::class, 'update']
    )
        ->whereNumber('miscFeeComponent')
        ->middleware(
            'permission:misc-fee-component.edit'
        )
        ->name('misc-fee-components.update');


    Route::delete(
        '/fees/setup/misc-components/{miscFeeComponent}',
        [MiscFeeComponentController::class, 'destroy']
    )
        ->whereNumber('miscFeeComponent')
        ->middleware(
            'permission:misc-fee-component.delete'
        )
        ->name('misc-fee-components.destroy');    

    Route::get(
        '/fees/setup/banks',
        [BankMasterController::class, 'index']
    )
        ->middleware('permission:bank-master.view')
        ->name('bank-masters.index');


    Route::get(
        '/fees/setup/banks/create',
        [BankMasterController::class, 'create']
    )
        ->middleware('permission:bank-master.create')
        ->name('bank-masters.create');


    Route::post(
        '/fees/setup/banks',
        [BankMasterController::class, 'store']
    )
        ->middleware('permission:bank-master.create')
        ->name('bank-masters.store');


    Route::get(
        '/fees/setup/banks/{bankMaster}/edit',
        [BankMasterController::class, 'edit']
    )
        ->whereNumber('bankMaster')
        ->middleware('permission:bank-master.edit')
        ->name('bank-masters.edit');


    Route::put(
        '/fees/setup/banks/{bankMaster}',
        [BankMasterController::class, 'update']
    )
        ->whereNumber('bankMaster')
        ->middleware('permission:bank-master.edit')
        ->name('bank-masters.update');


    Route::delete(
        '/fees/setup/banks/{bankMaster}',
        [BankMasterController::class, 'destroy']
    )
        ->whereNumber('bankMaster')
        ->middleware('permission:bank-master.delete')
        ->name('bank-masters.destroy'); 
        
    Route::get(
        '/fees/setup/school-accounts',
        [SchoolAccountController::class, 'index']
    )
        ->middleware('permission:school-account.view')
        ->name('school-accounts.index');


    Route::get(
        '/fees/setup/school-accounts/create',
        [SchoolAccountController::class, 'create']
    )
        ->middleware('permission:school-account.create')
        ->name('school-accounts.create');


    Route::post(
        '/fees/setup/school-accounts',
        [SchoolAccountController::class, 'store']
    )
        ->middleware('permission:school-account.create')
        ->name('school-accounts.store');


    Route::get(
        '/fees/setup/school-accounts/{schoolAccount}/edit',
        [SchoolAccountController::class, 'edit']
    )
        ->whereNumber('schoolAccount')
        ->middleware('permission:school-account.edit')
        ->name('school-accounts.edit');


    Route::put(
        '/fees/setup/school-accounts/{schoolAccount}',
        [SchoolAccountController::class, 'update']
    )
        ->whereNumber('schoolAccount')
        ->middleware('permission:school-account.edit')
        ->name('school-accounts.update');


    Route::delete(
        '/fees/setup/school-accounts/{schoolAccount}',
        [SchoolAccountController::class, 'destroy']
    )
        ->whereNumber('schoolAccount')
        ->middleware('permission:school-account.delete')
        ->name('school-accounts.destroy');    

    Route::get(
        '/fees/setup/receipt-schemes',
        [FeeReceiptSchemeController::class, 'index']
    )
        ->middleware('permission:fee-receipt-scheme.view')
        ->name('fee-receipt-schemes.index');


    Route::get(
        '/fees/setup/receipt-schemes/create',
        [FeeReceiptSchemeController::class, 'create']
    )
        ->middleware('permission:fee-receipt-scheme.create')
        ->name('fee-receipt-schemes.create');


    Route::post(
        '/fees/setup/receipt-schemes',
        [FeeReceiptSchemeController::class, 'store']
    )
        ->middleware('permission:fee-receipt-scheme.create')
        ->name('fee-receipt-schemes.store');


    Route::get(
        '/fees/setup/receipt-schemes/{feeReceiptScheme}/edit',
        [FeeReceiptSchemeController::class, 'edit']
    )
        ->whereNumber('feeReceiptScheme')
        ->middleware('permission:fee-receipt-scheme.edit')
        ->name('fee-receipt-schemes.edit');


    Route::put(
        '/fees/setup/receipt-schemes/{feeReceiptScheme}',
        [FeeReceiptSchemeController::class, 'update']
    )
        ->whereNumber('feeReceiptScheme')
        ->middleware('permission:fee-receipt-scheme.edit')
        ->name('fee-receipt-schemes.update');


    Route::delete(
        '/fees/setup/receipt-schemes/{feeReceiptScheme}',
        [FeeReceiptSchemeController::class, 'destroy']
    )
        ->whereNumber('feeReceiptScheme')
        ->middleware('permission:fee-receipt-scheme.delete')
        ->name('fee-receipt-schemes.destroy');

    Route::get(
        '/fees/setup/late-fee-fines',
        [LateFeeFineRuleController::class, 'index']
    )
        ->middleware('permission:late-fee-fine.view')
        ->name('late-fee-fines.index');


    Route::get(
        '/fees/setup/late-fee-fines/create',
        [LateFeeFineRuleController::class, 'create']
    )
        ->middleware('permission:late-fee-fine.create')
        ->name('late-fee-fines.create');


    Route::post(
        '/fees/setup/late-fee-fines',
        [LateFeeFineRuleController::class, 'store']
    )
        ->middleware('permission:late-fee-fine.create')
        ->name('late-fee-fines.store');


    Route::get(
        '/fees/setup/late-fee-fines/{lateFeeFine}/edit',
        [LateFeeFineRuleController::class, 'edit']
    )
        ->whereNumber('lateFeeFine')
        ->middleware('permission:late-fee-fine.edit')
        ->name('late-fee-fines.edit');


    Route::put(
        '/fees/setup/late-fee-fines/{lateFeeFine}',
        [LateFeeFineRuleController::class, 'update']
    )
        ->whereNumber('lateFeeFine')
        ->middleware('permission:late-fee-fine.edit')
        ->name('late-fee-fines.update');


    Route::delete(
        '/fees/setup/late-fee-fines/{lateFeeFine}',
        [LateFeeFineRuleController::class, 'destroy']
    )
        ->whereNumber('lateFeeFine')
        ->middleware('permission:late-fee-fine.delete')
        ->name('late-fee-fines.destroy');
        
    Route::get(
        '/fees/setup/concession-types',
        [ConcessionTypeController::class, 'index']
    )
        ->middleware(
            'permission:concession-type.view'
        )
        ->name('concession-types.index');


    Route::get(
        '/fees/setup/concession-types/create',
        [ConcessionTypeController::class, 'create']
    )
        ->middleware(
            'permission:concession-type.create'
        )
        ->name('concession-types.create');


    Route::post(
        '/fees/setup/concession-types',
        [ConcessionTypeController::class, 'store']
    )
        ->middleware(
            'permission:concession-type.create'
        )
        ->name('concession-types.store');


    Route::get(
        '/fees/setup/concession-types/{concessionType}/edit',
        [ConcessionTypeController::class, 'edit']
    )
        ->whereNumber('concessionType')
        ->middleware(
            'permission:concession-type.edit'
        )
        ->name('concession-types.edit');


    Route::put(
        '/fees/setup/concession-types/{concessionType}',
        [ConcessionTypeController::class, 'update']
    )
        ->whereNumber('concessionType')
        ->middleware(
            'permission:concession-type.edit'
        )
        ->name('concession-types.update');


    Route::delete(
        '/fees/setup/concession-types/{concessionType}',
        [ConcessionTypeController::class, 'destroy']
    )
        ->whereNumber('concessionType')
        ->middleware(
            'permission:concession-type.delete'
        )
        ->name('concession-types.destroy');

    Route::get(
        '/fees/setup/payment-modes',
        [PaymentModeController::class, 'index']
    )
        ->middleware('permission:payment-mode.view')
        ->name('payment-modes.index');


    Route::get(
        '/fees/setup/payment-modes/create',
        [PaymentModeController::class, 'create']
    )
        ->middleware('permission:payment-mode.create')
        ->name('payment-modes.create');


    Route::post(
        '/fees/setup/payment-modes',
        [PaymentModeController::class, 'store']
    )
        ->middleware('permission:payment-mode.create')
        ->name('payment-modes.store');


    Route::get(
        '/fees/setup/payment-modes/{paymentMode}/edit',
        [PaymentModeController::class, 'edit']
    )
        ->whereNumber('paymentMode')
        ->middleware('permission:payment-mode.edit')
        ->name('payment-modes.edit');


    Route::put(
        '/fees/setup/payment-modes/{paymentMode}',
        [PaymentModeController::class, 'update']
    )
        ->whereNumber('paymentMode')
        ->middleware('permission:payment-mode.edit')
        ->name('payment-modes.update');


    Route::delete(
        '/fees/setup/payment-modes/{paymentMode}',
        [PaymentModeController::class, 'destroy']
    )
        ->whereNumber('paymentMode')
        ->middleware('permission:payment-mode.delete')
        ->name('payment-modes.destroy');    

    Route::get(
        '/fees/setup/cheque-bounce-reasons',
        [ChequeBounceReasonController::class, 'index']
    )
        ->middleware(
            'permission:cheque-bounce-reason.view'
        )
        ->name(
            'cheque-bounce-reasons.index'
        );


    Route::get(
        '/fees/setup/cheque-bounce-reasons/create',
        [ChequeBounceReasonController::class, 'create']
    )
        ->middleware(
            'permission:cheque-bounce-reason.create'
        )
        ->name(
            'cheque-bounce-reasons.create'
        );


    Route::post(
        '/fees/setup/cheque-bounce-reasons',
        [ChequeBounceReasonController::class, 'store']
    )
        ->middleware(
            'permission:cheque-bounce-reason.create'
        )
        ->name(
            'cheque-bounce-reasons.store'
        );


    Route::get(
        '/fees/setup/cheque-bounce-reasons/{chequeBounceReason}/edit',
        [ChequeBounceReasonController::class, 'edit']
    )
        ->whereNumber('chequeBounceReason')
        ->middleware(
            'permission:cheque-bounce-reason.edit'
        )
        ->name(
            'cheque-bounce-reasons.edit'
        );


    Route::put(
        '/fees/setup/cheque-bounce-reasons/{chequeBounceReason}',
        [ChequeBounceReasonController::class, 'update']
    )
        ->whereNumber('chequeBounceReason')
        ->middleware(
            'permission:cheque-bounce-reason.edit'
        )
        ->name(
            'cheque-bounce-reasons.update'
        );


    Route::delete(
        '/fees/setup/cheque-bounce-reasons/{chequeBounceReason}',
        [ChequeBounceReasonController::class, 'destroy']
    )
        ->whereNumber('chequeBounceReason')
        ->middleware(
            'permission:cheque-bounce-reason.delete'
        )
        ->name(
            'cheque-bounce-reasons.destroy'
        );    

    Route::get(
        '/fees/compile',
        [FeeCompileController::class, 'index']
    )
    ->middleware(
        'permission:student-fee-assignment.bulk-create'
    )
    ->name('fee-compile.index');


    Route::get(
        '/fees/compile/sections',
        [FeeCompileController::class, 'sections']
    )
    ->middleware(
        'permission:student-fee-assignment.bulk-create'
    )
    ->name('fee-compile.sections');


    Route::get(
        '/fees/compile/templates',
        [FeeCompileController::class, 'templates']
    )
    ->middleware(
        'permission:student-fee-assignment.bulk-create'
    )
    ->name('fee-compile.templates');


    Route::post(
        '/fees/compile/preview',
        [FeeCompileController::class, 'preview']
    )
    ->middleware(
        'permission:student-fee-assignment.bulk-create'
    )
    ->name('fee-compile.preview');


    Route::post(
        '/fees/compile/process',
        [FeeCompileController::class, 'compile']
    )
    ->middleware(
        'permission:student-fee-assignment.bulk-create'
    )
    ->name('fee-compile.process');    
    
    
    Route::get(
        '/fees/compile-queues',
        [FeeCompileQueueController::class, 'index']
    )
    ->middleware(
        'permission:fee-compile-queue.view'
    )
    ->name(
        'fee-compile-queues.index'
    );


    Route::get(
        '/fees/compile-queues/{feeCompileQueue}',
        [FeeCompileQueueController::class, 'show']
    )
    ->whereNumber('feeCompileQueue')
    ->middleware(
        'permission:fee-compile-queue.view'
    )
    ->name(
        'fee-compile-queues.show'
    );


    Route::post(
        '/fees/compile-queues/{feeCompileQueue}/cancel',
        [FeeCompileQueueController::class, 'cancel']
    )
    ->whereNumber('feeCompileQueue')
    ->middleware(
        'permission:fee-compile-queue.cancel'
    )
    ->name(
        'fee-compile-queues.cancel'
    );


    Route::post(
        '/fees/compile-queues/{feeCompileQueue}/retry',
        [FeeCompileQueueController::class, 'retry']
    )
    ->whereNumber('feeCompileQueue')
    ->middleware(
        'permission:fee-compile-queue.retry'
    )
    ->name(
        'fee-compile-queues.retry'
    );

    Route::get(
        '/fees/optional-assignments',
        [OptionalFeeAssignmentController::class, 'index']
    )
    ->middleware(
        'permission:optional-fee-assignment.view'
    )
    ->name(
        'optional-fee-assignments.index'
    );


    Route::get(
        '/fees/optional-assignments/create',
        [OptionalFeeAssignmentController::class, 'create']
    )
    ->middleware(
        'permission:optional-fee-assignment.create'
    )
    ->name(
        'optional-fee-assignments.create'
    );


    Route::get(
        '/fees/optional-assignments/students',
        [OptionalFeeAssignmentController::class, 'students']
    )
    ->middleware(
        'permission:optional-fee-assignment.create'
    )
    ->name(
        'optional-fee-assignments.students'
    );


    Route::post(
        '/fees/optional-assignments',
        [OptionalFeeAssignmentController::class, 'store']
    )
    ->middleware(
        'permission:optional-fee-assignment.create'
    )
    ->name(
        'optional-fee-assignments.store'
    );


    Route::delete(
        '/fees/optional-assignments/{optionalFeeAssignment}',
        [OptionalFeeAssignmentController::class, 'destroy']
    )
    ->whereNumber('optionalFeeAssignment')
    ->middleware(
        'permission:optional-fee-assignment.delete'
    )
    ->name(
        'optional-fee-assignments.destroy'
    );

    // =====================================================
    // CONCESSION ASSIGNMENTS
    // =====================================================

    Route::get(
        '/fees/concession-assignments',
        [ConcessionAssignmentController::class, 'index']
    )
    ->middleware(['auth', 'permission:concession-assignment.view'])
    ->name('concession-assignments.index');


    Route::get(
        '/fees/concession-assignments/create',
        [ConcessionAssignmentController::class, 'create']
    )
    ->middleware(['auth', 'permission:concession-assignment.create'])
    ->name('concession-assignments.create');


    Route::post(
        '/fees/concession-assignments',
        [ConcessionAssignmentController::class, 'store']
    )
    ->middleware(['auth', 'permission:concession-assignment.create'])
    ->name('concession-assignments.store');


    // AJAX - Student Search
    Route::get(
        '/fees/concession-assignments/students',
        [ConcessionAssignmentController::class, 'students']
    )
    ->middleware(['auth', 'permission:concession-assignment.create'])
    ->name('concession-assignments.students');


    // AJAX - Student Fee Dues
    Route::get(
        '/fees/concession-assignments/dues',
        [ConcessionAssignmentController::class, 'dues']
    )
    ->middleware(['auth', 'permission:concession-assignment.create'])
    ->name('concession-assignments.dues');


    // Approve
    Route::post(
        '/fees/concession-assignments/{concessionAssignment}/approve',
        [ConcessionAssignmentController::class, 'approve']
    )
    ->middleware(['auth', 'permission:concession-assignment.approve'])
    ->name('concession-assignments.approve');


    // Reject
    Route::post(
        '/fees/concession-assignments/{concessionAssignment}/reject',
        [ConcessionAssignmentController::class, 'reject']
    )
    ->middleware(['auth', 'permission:concession-assignment.approve'])
    ->name('concession-assignments.reject');


    // Delete / Reverse
    Route::delete(
        '/fees/concession-assignments/{concessionAssignment}',
        [ConcessionAssignmentController::class, 'destroy']
    )
    ->middleware(['auth', 'permission:concession-assignment.delete'])
    ->name('concession-assignments.destroy');

    // =====================================================
    // FEE WAIVER ASSIGNMENTS
    // =====================================================

    Route::get(
        '/fees/waiver-assignments',
        [FeeWaiverAssignmentController::class, 'index']
    )
    ->middleware([
        'auth',
        'permission:fee-waiver-assignment.view'
    ])
    ->name('fee-waiver-assignments.index');


    Route::get(
        '/fees/waiver-assignments/create',
        [FeeWaiverAssignmentController::class, 'create']
    )
    ->middleware([
        'auth',
        'permission:fee-waiver-assignment.create'
    ])
    ->name('fee-waiver-assignments.create');


    Route::get(
        '/fees/waiver-assignments/students',
        [FeeWaiverAssignmentController::class, 'students']
    )
    ->middleware([
        'auth',
        'permission:fee-waiver-assignment.create'
    ])
    ->name('fee-waiver-assignments.students');


    Route::get(
        '/fees/waiver-assignments/dues',
        [FeeWaiverAssignmentController::class, 'dues']
    )
    ->middleware([
        'auth',
        'permission:fee-waiver-assignment.create'
    ])
    ->name('fee-waiver-assignments.dues');


    Route::post(
        '/fees/waiver-assignments',
        [FeeWaiverAssignmentController::class, 'store']
    )
    ->middleware([
        'auth',
        'permission:fee-waiver-assignment.create'
    ])
    ->name('fee-waiver-assignments.store');


    Route::post(
        '/fees/waiver-assignments/{feeWaiverAssignment}/approve',
        [FeeWaiverAssignmentController::class, 'approve']
    )
    ->middleware([
        'auth',
        'permission:fee-waiver-assignment.approve'
    ])
    ->name('fee-waiver-assignments.approve');


    Route::post(
        '/fees/waiver-assignments/{feeWaiverAssignment}/reject',
        [FeeWaiverAssignmentController::class, 'reject']
    )
    ->middleware([
        'auth',
        'permission:fee-waiver-assignment.approve'
    ])
    ->name('fee-waiver-assignments.reject');


    Route::delete(
        '/fees/waiver-assignments/{feeWaiverAssignment}',
        [FeeWaiverAssignmentController::class, 'destroy']
    )
    ->middleware([
        'auth',
        'permission:fee-waiver-assignment.delete'
    ])
    ->name('fee-waiver-assignments.destroy');

    // =====================================================
    // FINE WAIVER ASSIGNMENTS
    // =====================================================

    Route::get(
        '/fees/fine-waiver-assignments',
        [FineWaiverAssignmentController::class, 'index']
    )
    ->middleware([
        'auth',
        'permission:fine-waiver-assignment.view'
    ])
    ->name('fine-waiver-assignments.index');


    Route::get(
        '/fees/fine-waiver-assignments/create',
        [FineWaiverAssignmentController::class, 'create']
    )
    ->middleware([
        'auth',
        'permission:fine-waiver-assignment.create'
    ])
    ->name('fine-waiver-assignments.create');


    Route::get(
        '/fees/fine-waiver-assignments/students',
        [FineWaiverAssignmentController::class, 'students']
    )
    ->middleware([
        'auth',
        'permission:fine-waiver-assignment.create'
    ])
    ->name('fine-waiver-assignments.students');


    Route::get(
        '/fees/fine-waiver-assignments/dues',
        [FineWaiverAssignmentController::class, 'dues']
    )
    ->middleware([
        'auth',
        'permission:fine-waiver-assignment.create'
    ])
    ->name('fine-waiver-assignments.dues');


    Route::post(
        '/fees/fine-waiver-assignments',
        [FineWaiverAssignmentController::class, 'store']
    )
    ->middleware([
        'auth',
        'permission:fine-waiver-assignment.create'
    ])
    ->name('fine-waiver-assignments.store');


    Route::post(
        '/fees/fine-waiver-assignments/{fineWaiverAssignment}/approve',
        [FineWaiverAssignmentController::class, 'approve']
    )
    ->middleware([
        'auth',
        'permission:fine-waiver-assignment.approve'
    ])
    ->name('fine-waiver-assignments.approve');


    Route::post(
        '/fees/fine-waiver-assignments/{fineWaiverAssignment}/reject',
        [FineWaiverAssignmentController::class, 'reject']
    )
    ->middleware([
        'auth',
        'permission:fine-waiver-assignment.approve'
    ])
    ->name('fine-waiver-assignments.reject');


    Route::delete(
        '/fees/fine-waiver-assignments/{fineWaiverAssignment}',
        [FineWaiverAssignmentController::class, 'destroy']
    )
    ->middleware([
        'auth',
        'permission:fine-waiver-assignment.delete'
    ])
    ->name('fine-waiver-assignments.destroy');

    /*
    |--------------------------------------------------------------------------
    | Composite Concession
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/fees/composite-concessions',
        [CompositeConcessionController::class, 'index']
    )
    ->middleware('permission:composite-concession.view')
    ->name('composite-concessions.index');


    Route::get(
        '/fees/composite-concessions/create',
        [CompositeConcessionController::class, 'create']
    )
    ->middleware('permission:composite-concession.create')
    ->name('composite-concessions.create');


    /*
    |--------------------------------------------------------------------------
    | AJAX
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/fees/composite-concessions/students',
        [CompositeConcessionController::class, 'students']
    )
    ->middleware('permission:composite-concession.create')
    ->name('composite-concessions.students');


    Route::get(
        '/fees/composite-concessions/dues',
        [CompositeConcessionController::class, 'dues']
    )
    ->middleware('permission:composite-concession.create')
    ->name('composite-concessions.dues');


    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/fees/composite-concessions',
        [CompositeConcessionController::class, 'store']
    )
    ->middleware('permission:composite-concession.create')
    ->name('composite-concessions.store');


    /*
    |--------------------------------------------------------------------------
    | Approval
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/fees/composite-concessions/{compositeConcession}/approve',
        [CompositeConcessionController::class, 'approve']
    )
    ->middleware('permission:composite-concession.approve')
    ->name('composite-concessions.approve');


    Route::post(
        '/fees/composite-concessions/{compositeConcession}/reject',
        [CompositeConcessionController::class, 'reject']
    )
    ->middleware('permission:composite-concession.approve')
    ->name('composite-concessions.reject');


    /*
    |--------------------------------------------------------------------------
    | Delete / Reverse
    |--------------------------------------------------------------------------
    */

    Route::delete(
        '/fees/composite-concessions/{compositeConcession}',
        [CompositeConcessionController::class, 'destroy']
    )
    ->middleware('permission:composite-concession.delete')
    ->name('composite-concessions.destroy');

    Route::get(
        '/fees/receipts',
        [FeeReceiptController::class, 'index']
    )
    ->middleware('permission:fee-collection.view')
    ->name('fee-receipts.index');


    Route::get(
        '/fees/receipts/create',
        [FeeReceiptController::class, 'create']
    )
    ->middleware('permission:fee-collection.create')
    ->name('fee-receipts.create');


    Route::get(
        '/fees/receipts/students',
        [FeeReceiptController::class, 'students']
    )
    ->middleware('permission:fee-collection.create')
    ->name('fee-receipts.students');


    Route::get(
        '/fees/receipts/dues',
        [FeeReceiptController::class, 'dues']
    )
    ->middleware('permission:fee-collection.create')
    ->name('fee-receipts.dues');


    Route::post(
        '/fees/receipts',
        [FeeReceiptController::class, 'store']
    )
    ->middleware('permission:fee-collection.create')
    ->name('fee-receipts.store');


    Route::get(
        '/fees/receipts/{feeReceipt}',
        [FeeReceiptController::class, 'show']
    )
    ->middleware('permission:fee-collection.view')
    ->name('fee-receipts.show');


    Route::get(
        '/fees/receipts/{feeReceipt}/print',
        [FeeReceiptController::class, 'print']
    )
    ->middleware('permission:fee-collection.receipt')
    ->name('fee-receipts.print');


    Route::post(
        '/fees/receipts/{feeReceipt}/cancel',
        [FeeReceiptController::class, 'cancel']
    )
    ->middleware('permission:fee-collection.cancel')
    ->name('fee-receipts.cancel');


    /*
    |--------------------------------------------------------------------------
    | Fee Refund
    |--------------------------------------------------------------------------
    */

    Route::get('/fees/refunds', [FeeRefundController::class, 'index'])
        ->middleware('permission:fee-refund.view')
        ->name('fee-refunds.index');

    Route::get('/fees/refunds/create', [FeeRefundController::class, 'create'])
        ->middleware('permission:fee-refund.create')
        ->name('fee-refunds.create');

    /*
    |--------------------------------------------------------------------------
    | AJAX
    |--------------------------------------------------------------------------
    */

    Route::get('/fees/refunds/students', [FeeRefundController::class, 'students'])
        ->middleware('permission:fee-refund.create')
        ->name('fee-refunds.students');

    Route::get('/fees/refunds/receipts', [FeeRefundController::class, 'receipts'])
        ->middleware('permission:fee-refund.create')
        ->name('fee-refunds.receipts');

    Route::get('/fees/refunds/items', [FeeRefundController::class, 'items'])
        ->middleware('permission:fee-refund.create')
        ->name('fee-refunds.items');

    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    Route::post('/fees/refunds', [FeeRefundController::class, 'store'])
        ->middleware('permission:fee-refund.create')
        ->name('fee-refunds.store');

    /*
    |--------------------------------------------------------------------------
    | View / Print / Cancel
    |--------------------------------------------------------------------------
    */

    Route::get('/fees/refunds/{feeRefund}', [FeeRefundController::class, 'show'])
        ->middleware('permission:fee-refund.view')
        ->name('fee-refunds.show');

    Route::get('/fees/refunds/{feeRefund}/print', [FeeRefundController::class, 'print'])
        ->middleware('permission:fee-refund.receipt')
        ->name('fee-refunds.print');

    Route::post('/fees/refunds/{feeRefund}/cancel', [FeeRefundController::class, 'cancel'])
        ->middleware('permission:fee-refund.cancel')
        ->name('fee-refunds.cancel');

    /*
    |--------------------------------------------------------------------------
    | Fee Collection - Cheque / DD Details
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/fees/cheque-dd-details',
        [FeeChequeDdDetailController::class, 'index']
    )
    ->middleware(['auth', 'permission:cheque-dd.view'])
    ->name('cheque-dd-details.index');


    Route::get(
        '/fees/cheque-dd-details/create',
        [FeeChequeDdDetailController::class, 'create']
    )
    ->middleware(['auth', 'permission:cheque-dd.create'])
    ->name('cheque-dd-details.create');


    /*
    |--------------------------------------------------------------------------
    | AJAX - Receipt Details
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/fees/cheque-dd-details/receipt-details',
        [FeeChequeDdDetailController::class, 'receiptDetails']
    )
    ->middleware(['auth', 'permission:cheque-dd.create'])
    ->name('cheque-dd-details.receipt-details');


    /*
    |--------------------------------------------------------------------------
    | Store
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/fees/cheque-dd-details',
        [FeeChequeDdDetailController::class, 'store']
    )
    ->middleware(['auth', 'permission:cheque-dd.create'])
    ->name('cheque-dd-details.store');


    /*
    |--------------------------------------------------------------------------
    | Show
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/fees/cheque-dd-details/{chequeDdDetail}',
        [FeeChequeDdDetailController::class, 'show']
    )
    ->middleware(['auth', 'permission:cheque-dd.view'])
    ->name('cheque-dd-details.show');


    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/fees/cheque-dd-details/{chequeDdDetail}/edit',
        [FeeChequeDdDetailController::class, 'edit']
    )
    ->middleware(['auth', 'permission:cheque-dd.edit'])
    ->name('cheque-dd-details.edit');


    Route::put(
        '/fees/cheque-dd-details/{chequeDdDetail}',
        [FeeChequeDdDetailController::class, 'update']
    )
    ->middleware(['auth', 'permission:cheque-dd.edit'])
    ->name('cheque-dd-details.update');


    /*
    |--------------------------------------------------------------------------
    | Deposit
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/fees/cheque-dd-details/{chequeDdDetail}/deposit',
        [FeeChequeDdDetailController::class, 'deposit']
    )
    ->middleware(['auth', 'permission:cheque-dd.deposit'])
    ->name('cheque-dd-details.deposit');


    /*
    |--------------------------------------------------------------------------
    | Clear
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/fees/cheque-dd-details/{chequeDdDetail}/clear',
        [FeeChequeDdDetailController::class, 'clear']
    )
    ->middleware(['auth', 'permission:cheque-dd.clear'])
    ->name('cheque-dd-details.clear');


    /*
    |--------------------------------------------------------------------------
    | Bounce
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/fees/cheque-dd-details/{chequeDdDetail}/bounce',
        [FeeChequeDdDetailController::class, 'bounce']
    )
    ->middleware(['auth', 'permission:cheque-dd.bounce'])
    ->name('cheque-dd-details.bounce');


    /*
    |--------------------------------------------------------------------------
    | Cancel Tracking
    |--------------------------------------------------------------------------
    */

    Route::post(
        '/fees/cheque-dd-details/{chequeDdDetail}/cancel',
        [FeeChequeDdDetailController::class, 'cancel']
    )
    ->middleware(['auth', 'permission:cheque-dd.cancel'])
    ->name('cheque-dd-details.cancel');  
    

    Route::get(
        '/students/masters/tc-reasons',
        [TcReasonController::class, 'index']
    )
    ->middleware('permission:tc-reason.view')
    ->name('tc-reasons.index');


    Route::get(
        '/students/masters/tc-reasons/create',
        [TcReasonController::class, 'create']
    )
    ->middleware('permission:tc-reason.create')
    ->name('tc-reasons.create');


    Route::post(
        '/students/masters/tc-reasons',
        [TcReasonController::class, 'store']
    )
    ->middleware('permission:tc-reason.create')
    ->name('tc-reasons.store');


    Route::get(
        '/students/masters/tc-reasons/{tcReason}/edit',
        [TcReasonController::class, 'edit']
    )
    ->middleware('permission:tc-reason.edit')
    ->name('tc-reasons.edit');


    Route::put(
        '/students/masters/tc-reasons/{tcReason}',
        [TcReasonController::class, 'update']
    )
    ->middleware('permission:tc-reason.edit')
    ->name('tc-reasons.update');


    Route::delete(
        '/students/masters/tc-reasons/{tcReason}',
        [TcReasonController::class, 'destroy']
    )
    ->middleware('permission:tc-reason.delete')
    ->name('tc-reasons.destroy');

    Route::get(
        '/students/masters/tc-remark-options',
        [TcRemarkOptionController::class, 'index']
    )
    ->middleware('permission:tc-remark-option.view')
    ->name('tc-remark-options.index');


    Route::get(
        '/students/masters/tc-remark-options/create',
        [TcRemarkOptionController::class, 'create']
    )
    ->middleware('permission:tc-remark-option.create')
    ->name('tc-remark-options.create');


    Route::post(
        '/students/masters/tc-remark-options',
        [TcRemarkOptionController::class, 'store']
    )
    ->middleware('permission:tc-remark-option.create')
    ->name('tc-remark-options.store');


    Route::get(
        '/students/masters/tc-remark-options/{tcRemarkOption}/edit',
        [TcRemarkOptionController::class, 'edit']
    )
    ->middleware('permission:tc-remark-option.edit')
    ->name('tc-remark-options.edit');


    Route::put(
        '/students/masters/tc-remark-options/{tcRemarkOption}',
        [TcRemarkOptionController::class, 'update']
    )
    ->middleware('permission:tc-remark-option.edit')
    ->name('tc-remark-options.update');


    Route::delete(
        '/students/masters/tc-remark-options/{tcRemarkOption}',
        [TcRemarkOptionController::class, 'destroy']
    )
    ->middleware('permission:tc-remark-option.delete')
    ->name('tc-remark-options.destroy');

    
    Route::get(
        '/students/masters/tc-last-result-options',
        [TcLastResultOptionController::class, 'index']
    )
    ->middleware('permission:tc-last-result-option.view')
    ->name('tc-last-result-options.index');

    Route::get(
        '/students/masters/tc-last-result-options/create',
        [TcLastResultOptionController::class, 'create']
    )
    ->middleware('permission:tc-last-result-option.create')
    ->name('tc-last-result-options.create');

    Route::post(
        '/students/masters/tc-last-result-options',
        [TcLastResultOptionController::class, 'store']
    )
    ->middleware('permission:tc-last-result-option.create')
    ->name('tc-last-result-options.store');

    Route::get(
        '/students/masters/tc-last-result-options/{tcLastResultOption}/edit',
        [TcLastResultOptionController::class, 'edit']
    )
    ->middleware('permission:tc-last-result-option.edit')
    ->name('tc-last-result-options.edit');

    Route::put(
        '/students/masters/tc-last-result-options/{tcLastResultOption}',
        [TcLastResultOptionController::class, 'update']
    )
    ->middleware('permission:tc-last-result-option.edit')
    ->name('tc-last-result-options.update');

    Route::delete(
        '/students/masters/tc-last-result-options/{tcLastResultOption}',
        [TcLastResultOptionController::class, 'destroy']
    )
    ->middleware('permission:tc-last-result-option.delete')
    ->name('tc-last-result-options.destroy');

    Route::get(
        '/students/masters/promotion-statuses',
        [PromotionStatusController::class, 'index']
    )
    ->middleware('permission:promotion-status.view')
    ->name('promotion-statuses.index');


    Route::get(
        '/students/masters/promotion-statuses/create',
        [PromotionStatusController::class, 'create']
    )
    ->middleware('permission:promotion-status.create')
    ->name('promotion-statuses.create');


    Route::post(
        '/students/masters/promotion-statuses',
        [PromotionStatusController::class, 'store']
    )
    ->middleware('permission:promotion-status.create')
    ->name('promotion-statuses.store');


    Route::get(
        '/students/masters/promotion-statuses/{promotionStatus}/edit',
        [PromotionStatusController::class, 'edit']
    )
    ->middleware('permission:promotion-status.edit')
    ->name('promotion-statuses.edit');


    Route::put(
        '/students/masters/promotion-statuses/{promotionStatus}',
        [PromotionStatusController::class, 'update']
    )
    ->middleware('permission:promotion-status.edit')
    ->name('promotion-statuses.update');


    Route::delete(
        '/students/masters/promotion-statuses/{promotionStatus}',
        [PromotionStatusController::class, 'destroy']
    )
    ->middleware('permission:promotion-status.delete')
    ->name('promotion-statuses.destroy');
});