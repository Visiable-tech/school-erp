<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::firstOrCreate(
            ['school_code' => 'SCH001'],
            [
                'school_name' => 'Demo School',
                'short_name' => 'DS',
                'email' => 'school@example.com',
                'phone' => '9999999999',
                'country' => 'India',
                'status' => true,
            ]
        );

        $user = User::updateOrCreate(
            ['email' => 'admin@schoolerp.com'],
            [
                'school_id' => $school->id,
                'name' => 'Super Admin',
                'password' => Hash::make('Admin@123'),
                'status' => true,
            ]
        );

        $user->syncRoles(['Super Admin']);
    }
}