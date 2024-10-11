<?php

namespace Database\Seeders;

use App\Models\MedicalProfile;
use App\Models\Sponsorship;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class SponsorshipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //? disabilito relazioni:
        Schema::disableForeignKeyConstraints();

        //? ripulisco tabella:
        Sponsorship::truncate();

        $sponsorships = config('sponsorships');


        foreach ($sponsorships as $new_sponsorship) {
            $sponsorship = new Sponsorship();
            $sponsorship->package = $new_sponsorship['package'];
            $sponsorship->price = $new_sponsorship['price'];
            $sponsorship->save();
        }

        //? abilito relazione:
        Schema::enableForeignKeyConstraints();
    }
}
