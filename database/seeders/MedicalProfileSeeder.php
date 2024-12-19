<?php

namespace Database\Seeders;

use App\Models\MedicalProfile;
use App\Models\Specialization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MedicalProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //? disabilito relazioni:
        Schema::disableForeignKeyConstraints();

        //? ripulisco tabella:
        MedicalProfile::truncate();

        $medicalProfiles = config('medical_profiles'); 

        foreach ($medicalProfiles as $profile) {
            $medical_profile = new MedicalProfile();

            $medical_profile->user_id = 10; 
            $medical_profile->specialization_id = Specialization::inRandomOrder()->first()->id;
            $medical_profile->slug = $profile['slug']; 
            $medical_profile->cv = $profile['cv']; 
            $medical_profile->photograph = $profile['photograph']; 
            $medical_profile->phone = $profile['phone']; 
            $medical_profile->address = $profile['address']; 

            $medical_profile->save(); 
        }
        //? abilito relazione:
        Schema::enableForeignKeyConstraints(); 
    }
}
