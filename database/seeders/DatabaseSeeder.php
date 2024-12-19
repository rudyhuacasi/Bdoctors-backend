<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\MedicinePerformance;
use App\Models\Message;
use App\Models\ProfilePerformance;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // fare la chiamata  a tutte le seeder
        $this->call([
            SpecializationSeeder::class,  
            MedicalProfileSeeder::class,
            MessageSeeder::class,
            SponsorshipSeeder::class,
            MedicinePerformance::class,
            ProfilePerformance::class,  
        ]);
    }
}
