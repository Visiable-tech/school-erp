<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentImportTemplateExport implements
    FromArray,
    WithHeadings,
    ShouldAutoSize
{
    public function headings(): array
    {
        return [

            'student_name',
            'admission_date',
            'date_of_birth',
            'gender',
            'blood_group',
            'nationality',
            'religion',
            'category',
            'mother_tongue',

            'academic_year',
            'class',
            'section',
            'roll_no',

            'father_name',
            'father_mobile',
            'father_email',
            'father_occupation',

            'mother_name',
            'mother_mobile',
            'mother_email',
            'mother_occupation',

            'guardian_name',
            'guardian_relation',
            'guardian_mobile',

            'present_address',
            'present_city',
            'present_state',
            'present_pin_code',

            'permanent_address',
            'permanent_city',
            'permanent_state',
            'permanent_pin_code',

            'previous_school',
            'previous_class',
            'previous_board',
        ];
    }


    public function array(): array
    {
        return [

            [
                'Rahul Sharma',
                '2026-09-22',
                '2020-05-10',
                'Male',
                'B+',
                'Indian',
                'Hindu',
                'General',
                'Hindi',

                '2026-2027',
                'Class I',
                'A',
                '1',

                'Rajesh Sharma',
                '9876543210',
                'rajesh@example.com',
                'Business',

                'Sunita Sharma',
                '9876543211',
                'sunita@example.com',
                'Teacher',

                '',
                '',
                '',

                '123 GT Road',
                'Howrah',
                'West Bengal',
                '711204',

                '123 GT Road',
                'Howrah',
                'West Bengal',
                '711204',

                'ABC Kids School',
                'UKG',
                'CBSE',
            ],

        ];
    }
}