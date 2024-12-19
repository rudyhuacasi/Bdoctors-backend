<?php

namespace Database\Seeders;

use App\Models\MedicalProfile;
use App\Models\Message;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Faker\Factory as Faker;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //? disabilito relazioni:
        Schema::disableForeignKeyConstraints();

        //? ripulisco tabella:
        Message::truncate();

        //? popolo random la tabella:
        for ($i = 0; $i < 20; $i++) {
            $faker = Faker::create();


            $new_message = new Message();

            $new_message->medical_profile_id = MedicalProfile::inRandomOrder()->first()->id;
            $new_message->email_sender = $faker->email();
            $new_message->content = $faker->text(200);

            $new_message->save();
        }



        //? riabilito relazioni:
        Schema::enableForeignKeyConstraints();

    }
}
