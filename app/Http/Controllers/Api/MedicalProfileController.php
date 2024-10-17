<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicalProfile;
use App\Http\Requests\StoreMedicalProfileRequest;
use App\Http\Requests\UpdateMedicalProfileRequest;

class MedicalProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $project = Project::all();

        $medical_profile = MedicalProfile::paginate(9);

        // $medical_profile = MedicalProfile::with('type', 'technologies')->get();
        return response()->json([
            'status' => 'success',
            'results' => $medical_profile,
        ]);
    }

    /**
     * Show the form for creating a new resource.
    */
    public function create()
    {
        
    }

    // /**
    // * Store a newly created resource in storage.
    //  */
    // public function store(StoreMedicalProfileRequest $request)
    // {
        
    // }

    /**
     * Display the specified resource.
     */
    public function show(String $slug)
    {
        //? dettaglio con relazione services:
        $medical_profile = MedicalProfile::where('slug', $slug)->with('reviews', 'user', 'medicalspecializations', 'payments', 'messages', 'statistics')->first();

        if ($medical_profile) {
            return response()->json([
                'status' => 'success',
                'results' => $medical_profile
            ]);
        } else {
            return response()->json([
                'status' => 'failed',
                'results' => null
            ], 404);
        }
    }

    // /**
    //  * Show the form for editing the specified resource.
    //  */
    // public function edit(MedicalProfile $medicalProfile)
    // {
        
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(UpdateMedicalProfileRequest $request, MedicalProfile $medicalProfile)
    // {
        
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    // public function destroy(MedicalProfile $medicalProfile)
    // {
        
    // }
}
