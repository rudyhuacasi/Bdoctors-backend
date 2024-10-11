<?php

namespace Database\Seeders;

use App\Models\Specialization;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class SpecializationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //? disabilito relazioni:
        Schema::disableForeignKeyConstraints();

        //? ripulisco tabella:
        Specialization::truncate();

        $specializations = config('specializations.specializations');

        
        foreach ($specializations as $new_specialization) {
            $specialization = new Specialization();
            $specialization->name = $new_specialization; 
            $specialization->save();
        }        

        //? abilito relazione:
        Schema::enableForeignKeyConstraints();
    }
}
