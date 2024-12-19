<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Http\Requests\StoreReviewRequest;
use App\Http\Requests\UpdateReviewRequest;
use App\Models\MedicalProfile;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Review $review, $id)
    {
        // validazione
        $validatedData = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email',
            'comment' => 'required|string|max:1000',
            'valuation' => 'required|integer|min:1|max:5',
            'medical_profile_id' => 'required|exists:medical_profiles,id'

        ]);
        $medical_profile = MedicalProfile::where('id', $id)->first();

        if (!$medical_profile) {
            return response()->json(['error' => 'non trovato.'], 404);
        }

        $review->medical_profile_id = $medical_profile->id;
        $review->full_name = $validatedData['full_name'];
        $review->email = $validatedData['email'];
        $review->comment = $validatedData['comment'];
        $review->valuation = $validatedData['valuation'];
        $review->creation_date = now();

        $review->save();
 
        return response()->json(['status' => 'success', 'message' => 'Valutazione fatto']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateReviewRequest $request, Review $review)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Review $review)
    {
        //
    }
}
