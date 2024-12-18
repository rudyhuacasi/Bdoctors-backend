<?php

namespace App\Observers;

use App\Models\Review;
use App\Models\Statistic;
use Illuminate\Support\Facades\Log;

class ReviewObserver
{
    /**
     * Handle the Review "created" event.
     */
    // funzione  per fare la media e contare il numero de valutazione fatto di ogni profilo 
    public function created(Review $review): void
    {
        $statistic = Statistic::firstOrCreate(
            ['medical_profile_id' => $review->medical_profile_id],
            ['update_date' => now()]
        );

        $valuations = Review::where('medical_profile_id', $review->medical_profile_id)
            ->whereNotNull('valuation')
            ->pluck('valuation');

        $averageValuation = $valuations->avg();


        $statistic->media = $averageValuation;
        $statistic->reviews_received += 1;
        $statistic->update_date = now();
        $statistic->save();
    }

    /**
     * Handle the Review "updated" event.
     */
    public function updated(Review $review): void
    {
        //
    }

    /**
     * Handle the Review "deleted" event.
     */
    public function deleted(Review $review): void
    {
        //
    }

    /**
     * Handle the Review "restored" event.
     */
    public function restored(Review $review): void
    {
        //
    }

    /**
     * Handle the Review "force deleted" event.
     */
    public function forceDeleted(Review $review): void
    {
        //
    }
}
