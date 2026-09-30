<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Clear Permission Cache
        |--------------------------------------------------------------------------
        */

        app()[PermissionRegistrar::class]
            ->forgetCachedPermissions();


        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

        $permissions = [

            // School
            'school.view',
            'school.create',
            'school.edit',
            'school.delete',

            // Academic Year
            'academic-year.view',
            'academic-year.create',
            'academic-year.edit',
            'academic-year.delete',

            // Wing
            'wing.view',
            'wing.create',
            'wing.edit',
            'wing.delete',

            // Class
            'class.view',
            'class.create',
            'class.edit',
            'class.delete',

            // Section
            'section.view',
            'section.create',
            'section.edit',
            'section.delete',

            // Subject
            'subject.view',
            'subject.create',
            'subject.edit',
            'subject.delete',

            // Class Subject
            'class-subject.view',
            'class-subject.create',
            'class-subject.edit',
            'class-subject.delete',

            // Section Group
            'section-group.view',
            'section-group.create',
            'section-group.edit',
            'section-group.delete',

            // Calendar
            'calendar.view',
            'calendar.create',
            'calendar.edit',
            'calendar.delete',


            /*
            |--------------------------------------------------------------------------
            | Admission
            |--------------------------------------------------------------------------
            */

            'admission-session.view',
            'admission-session.create',
            'admission-session.edit',
            'admission-session.delete',

            'admission-enquiry.view',
            'admission-enquiry.create',
            'admission-enquiry.edit',
            'admission-enquiry.delete',

            'admission-followup.view',
            'admission-followup.create',
            'admission-followup.edit',
            'admission-followup.delete',

            'admission-application.view',
            'admission-application.create',
            'admission-application.edit',
            'admission-application.delete',
            'admission-application.approve',

            'admission-document.view',
            'admission-document.create',
            'admission-document.verify',
            'admission-document.delete',

            'admission-application.review',
            'admission-application.reject',


            /*
            |--------------------------------------------------------------------------
            | Student
            |--------------------------------------------------------------------------
            */

            'student.view',
            'student.create',
            'student.edit',
            'student.delete',

            // Admission -> Student conversion
            'student-admission.create',

            // Enrollment
            'student-enrollment.view',
            'student-enrollment.create',
            'student-enrollment.edit',

            // Promotion
            'student-promotion.view',
            'student-promotion.create',

            'student-document.view',
            'student-document.create',
            'student-document.edit',
            'student-document.delete',

            'student-attendance.view',
            'student-attendance.create',
            'student-attendance.edit',
            'student-attendance.delete',
            'student-attendance.report',
            'student-attendance.import',

            'fee-head.view',
            'fee-head.create',
            'fee-head.edit',
            'fee-head.delete',

            'fee-structure.view',
            'fee-structure.create',
            'fee-structure.edit',
            'fee-structure.delete',
            'fee-structure.import',

            'fee-collection.view',
            'fee-collection.create',
            'fee-collection.cancel',
            'fee-collection.receipt',

            'fee-report.view',

            'fee-installment.view',
            'fee-installment.create',
            'fee-installment.edit',
            'fee-installment.delete',

            'student-fee-assignment.view',
            'student-fee-assignment.create',
            'student-fee-assignment.edit',
            'student-fee-assignment.delete',
            'student-fee-assignment.bulk-create',

            'student-fee-due.view',
            'student-fee-due.edit',

            'fee-cycle.view',
            'fee-cycle.create',
            'fee-cycle.edit',
            'fee-cycle.delete',

            'fee-component-group.view',
            'fee-component-group.create',
            'fee-component-group.edit',
            'fee-component-group.delete',

            'misc-fee-component.view',
            'misc-fee-component.create',
            'misc-fee-component.edit',
            'misc-fee-component.delete',

            'bank-master.view',
            'bank-master.create',
            'bank-master.edit',
            'bank-master.delete',

            'school-account.view',
            'school-account.create',
            'school-account.edit',
            'school-account.delete',

            'fee-receipt-scheme.view',
            'fee-receipt-scheme.create',
            'fee-receipt-scheme.edit',
            'fee-receipt-scheme.delete',

            'late-fee-fine.view',
            'late-fee-fine.create',
            'late-fee-fine.edit',
            'late-fee-fine.delete',

            'concession-type.view',
            'concession-type.create',
            'concession-type.edit',
            'concession-type.delete',

            'payment-mode.view',
            'payment-mode.create',
            'payment-mode.edit',
            'payment-mode.delete',

            'cheque-bounce-reason.view',
            'cheque-bounce-reason.create',
            'cheque-bounce-reason.edit',
            'cheque-bounce-reason.delete',

            'fee-compile-queue.view',
            'fee-compile-queue.cancel',
            'fee-compile-queue.retry',

            'optional-fee-assignment.view',
            'optional-fee-assignment.create',
            'optional-fee-assignment.delete',

            'concession-assignment.view',
            'concession-assignment.create',
            'concession-assignment.approve',
            'concession-assignment.delete',

            'fee-waiver-assignment.view',
            'fee-waiver-assignment.create',
            'fee-waiver-assignment.approve',
            'fee-waiver-assignment.delete',

            'fine-waiver-assignment.view',
            'fine-waiver-assignment.create',
            'fine-waiver-assignment.approve',
            'fine-waiver-assignment.delete',

            'composite-concession.view',
            'composite-concession.create',
            'composite-concession.approve',
            'composite-concession.delete',

            'fee-collection.view',
            'fee-collection.create',
            'fee-collection.cancel',
            'fee-collection.receipt',

            'fee-refund.view',
            'fee-refund.create',
            'fee-refund.cancel',
            'fee-refund.receipt',

            'fee-refund.view',
            'fee-refund.create',
            'fee-refund.cancel',
            'fee-refund.receipt',

            'cheque-dd.view',
            'cheque-dd.create',
            'cheque-dd.edit',
            'cheque-dd.deposit',
            'cheque-dd.clear',
            'cheque-dd.bounce',
            'cheque-dd.cancel',

            'tc-reason.view',
            'tc-reason.create',
            'tc-reason.edit',
            'tc-reason.delete',

            'tc-remark-option.view',
            'tc-remark-option.create',
            'tc-remark-option.edit',
            'tc-remark-option.delete',

            'tc-last-result-option.view',
            'tc-last-result-option.create',
            'tc-last-result-option.edit',
            'tc-last-result-option.delete',

            'promotion-status.view',
            'promotion-status.create',
            'promotion-status.edit',
            'promotion-status.delete',
        ];


        /*
        |--------------------------------------------------------------------------
        | Create Permissions
        |--------------------------------------------------------------------------
        */

        foreach ($permissions as $permission) {

            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $roles = [

            'Super Admin',
            'Admin',
            'Principal',
            'Coordinator',
            'Teacher',
            'Accounts',
            'HR',
            'Examination Incharge',
            'Admission',
            'Student',

        ];


        foreach ($roles as $roleName) {

            Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | Super Admin - All Permissions
        |--------------------------------------------------------------------------
        */

        $superAdmin = Role::where([
            'name' => 'Super Admin',
            'guard_name' => 'web',
        ])->firstOrFail();


        $superAdmin->syncPermissions(
            Permission::where(
                'guard_name',
                'web'
            )->get()
        );


        /*
        |--------------------------------------------------------------------------
        | Clear Cache Again
        |--------------------------------------------------------------------------
        */

        app()[PermissionRegistrar::class]
            ->forgetCachedPermissions();
    }
}