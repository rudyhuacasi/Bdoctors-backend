<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicalProfile;
// use App\Http\Requests\StoreMedicalProfileRequest;
// use App\Http\Requests\UpdateMedicalProfileRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MedicalProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $medical_profile = MedicalProfile::paginate(9);

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

    /**
    * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // validazione
        $request->validate([
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:255',
            'cv' => 'required|file|mimes:pdf,doc,docx,xlsx,txt|max:2048',
            'photograph' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->all();
        $medicalProfile = new MedicalProfile();

        $user = User::find(auth()->id());

        if (!$user) {
            return response()->json(['error' => 'Usuario no encontrado'], 404);
        }

        // ID dell'usuario
        $medicalProfile->user_id = $user->id;

        // dati per la tabella
        $firstName = $user->name; // nome della tabella user
        $lastName = $user->last_name;
        $medicalProfile->slug = 'dr-' . Str::slug($firstName . '-' . $lastName);        
        $medicalProfile->phone = $data['phone'];
        $medicalProfile->address = $data['address'];

        if ($request->hasFile('cv')) {
            $filePath = $request->file('cv')->store('cvs', 'public');
            $medicalProfile->cv = $filePath;
        }

        if ($request->hasFile('photograph')) {
            $photoPath = $request->file('photograph')->store('photographs', 'public');
            $medicalProfile->photograph = $photoPath;
        }

        $medicalProfile->save();

        return response()->json(['status' => 'success', 'message' => 'Perfil médico creado exitosamente.']);
    }

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
