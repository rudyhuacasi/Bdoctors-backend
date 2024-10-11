<?php

namespace Database\Seeders;

use App\Models\MedicalProfile;
use App\Models\MedicalSpecialization;
use App\Models\Specialization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MedicalSpecializationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //? disabilito relazioni:
        Schema::disableForeignKeyConstraints();

        //? ripulisco tabella:
        MedicalSpecialization::truncate();

        //? popoliamo random la tabella pivot:
            $medical_specialization = new MedicalSpecialization();

            $medical_specialization->medical_profile_id = MedicalProfile::inRandomOrder()->first()->id;
            $medical_specialization->specialization_id = Specialization::inRandomOrder()->first()->id;

            $medical_specialization->save();

        //? abilito relazione:
        Schema::enableForeignKeyConstraints();
    }
}
