<?php

namespace App\Observers;

use App\Models\Message;
use App\Models\Statistic;

class MessageObserver
{
    /**
     * Handle the Message "created" event.
     */
    // funzione che racconta el numero di messagi ricevuti
    public function created(Message $message): void
    {
        $medicalProfileId = $message->medical_profile_id;

        $statistic = Statistic::where('medical_profile_id', $medicalProfileId)->first();


        $statistic->messages_received += 1;
        $statistic->update_date = now();
        $statistic->save();
    }

    /**
     * Handle the Message "updated" event.
     */
    public function updated(Message $message): void
    {
        //
    }

    /**
     * Handle the Message "deleted" event.
     */
    public function deleted(Message $message): void
    {
        //
    }

    /**
     * Handle the Message "restored" event.
     */
    public function restored(Message $message): void
    {
        //
    }

    /**
     * Handle the Message "force deleted" event.
     */
    public function forceDeleted(Message $message): void
    {
        //
    }
}
