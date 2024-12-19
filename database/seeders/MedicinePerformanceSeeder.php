<?php

namespace Database\Seeders;

use App\Models\MedicinePerformance;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class MedicinePerformanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //? disabilito relazioni:
        Schema::disableForeignKeyConstraints();

        //? ripulisco tabella:
        MedicinePerformance::truncate();
        // config prestazione mediche
        $prestazioni = config('prestazioni_mediche');

        foreach ($prestazioni as $prestazione) {
            $prestazioniMediche= new MedicinePerformance();
            $prestazioniMediche->name = $prestazione['nome'];
            $prestazioniMediche->description = $prestazione['descrizione'];
            $prestazioniMediche->save();
        }

        //? abilito relazione:
        Schema::enableForeignKeyConstraints();
    }
}
