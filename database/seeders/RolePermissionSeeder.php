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

            'fee-report.view',

            'fee-installment.view',
            'fee-installment.create',
            'fee-installment.edit',
            'fee-installment.delete',
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