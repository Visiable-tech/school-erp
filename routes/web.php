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
    | STUDENT PROMOTION
    |--------------------------------------------------------------------------
    */

    Route::get(
        '/student-promotions',
        [StudentPromotionController::class, 'index']
    )
        ->middleware('permission:student-promotion.view')
        ->name('student-promotions.index');


    Route::get(
        '/student-promotions/sections',
        [StudentPromotionController::class, 'getSections']
    )
        ->middleware('permission:student-promotion.view')
        ->name('student-promotions.sections');


    Route::get(
        '/student-promotions/students',
        [StudentPromotionController::class, 'getStudents']
    )
        ->middleware('permission:student-promotion.view')
        ->name('student-promotions.students');


    Route::post(
        '/student-promotions',
        [StudentPromotionController::class, 'store']
    )
        ->middleware('permission:student-promotion.create')
        ->name('student-promotions.store');



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

});