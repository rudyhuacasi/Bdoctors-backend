<?php

namespace Database\Seeders;

use App\Models\MedicalProfile;
use App\Models\MedicinePerformance;
use App\Models\ProfilePerformance;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ProfilePerformanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //? Deshabilitar restricciones de clave foránea
        Schema::disableForeignKeyConstraints();

        //? Limpiar la tabla
        ProfilePerformance::truncate();

        // Crear relaciones de modo que cada perfil médico tenga entre 1 y 3 prestaciones
        $medicalProfiles = MedicalProfile::all();
        $performances = MedicinePerformance::all();

        foreach ($medicalProfiles as $profile) {
            // Generar un número aleatorio de relaciones (entre 1 y 3) para cada perfil médico
            $numRelations = rand(1, 16);

            for ($i = 0; $i < $numRelations; $i++) {
                // Crear una nueva instancia de la relación ProfilePerformance
                $profilePerformance = new ProfilePerformance();

                // Asignar el ID del perfil médico actual y un ID aleatorio de prestación
                $profilePerformance->medical_profile_id = $profile->id;
                $profilePerformance->medicine_performance_id = $performances->random()->id;

                // Guardar la relación en la base de datos
                $profilePerformance->save();
            }
        }

        //? Habilitar restricciones de clave foránea nuevamente
        Schema::enableForeignKeyConstraints();
    }
}
