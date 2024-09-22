<?php

namespace App\Http\Controllers;

use App\Models\MedicalProfiles;
use App\Http\Requests\StoreMedicalProfilesRequest;
use App\Http\Requests\UpdateMedicalProfilesRequest;

class MedicalProfilesController extends Controller
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
    public function store(StoreMedicalProfilesRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(MedicalProfiles $medicalProfiles)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MedicalProfiles $medicalProfiles)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMedicalProfilesRequest $request, MedicalProfiles $medicalProfiles)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicalProfiles $medicalProfiles)
    {
        //
    }
}
