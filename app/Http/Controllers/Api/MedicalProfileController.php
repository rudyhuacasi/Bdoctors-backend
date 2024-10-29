<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicalProfile;
// use App\Http\Requests\StoreMedicalProfileRequest;
// use App\Http\Requests\UpdateMedicalProfileRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
        // if (!$user) {
        //     return response()->json(['error' => 'Usuario no encontrado'], 404);
        // }

        // ID dell'usuario
        $medicalProfile->user_id = $user->id;

        // dati per la tabella
        $firstName = $user->name;
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
    public function show( $slug)
    {
        //? dettaglio con relazione services:
        $medical_profile = MedicalProfile::where('slug', $slug)
            ->with('reviews', 'user', 'medicalspecializations', 'payments', 'messages', 'statistics')
            ->first();

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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // Validación de los datos que se actualizarán
        $request->validate([
            'phone' => 'sometimes|string|max:15',
            'address' => 'sometimes|string|max:255',
            'cv' => 'sometimes|string',
            'photograph' => 'sometimes|string',
        ]);

        // Obtener el perfil médico existente
        $medicalProfile = MedicalProfile::findOrFail($id);
        if (!$medicalProfile) {
             return response()->json(['error' => 'Usuario no encontrado'], 404);
        }
        // Actualiza solo los campos que se enviaron en la solicitud
        if ($request->has('phone')) {
            $medicalProfile->phone = $request->input('phone');
        }

        if ($request->has('address')) {
            $medicalProfile->address = $request->input('address');
        }

        // Manejo del archivo CV si se envió como base64
        if ($request->has('cv')) {
            $cvData = $request->input('cv');
            // Aquí deberías procesar la cadena base64
            $filePath = 'cvs/' . uniqid() . '.pdf'; // Cambia la extensión según el tipo de archivo
            Storage::disk('public')->put($filePath, base64_decode($cvData)); // Almacena el archivo
            $medicalProfile->cv = $filePath;
        }

        if ($request->has('photograph')) {
            $photoData = $request->input('photograph');
            // Aquí deberías procesar la cadena base64
            $photoPath = 'photographs/' . uniqid() . '.png'; // Cambia la extensión según el tipo de imagen
            Storage::disk('public')->put($photoPath, base64_decode($photoData)); // Almacena el archivo
            $medicalProfile->photograph = $photoPath;
        }

         if ($medicalProfile->save()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Perfil médico actualizado exitosamente.',
                'updated_data' => $medicalProfile,
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Error al guardar los cambios en la base de datos.'
            ], 500);
        }    
    }

    // public function update(Request $request, MedicalProfile $medicalProfile, $id)
    // {
    //     // Validación de los datos que se actualizarán
    //     $data =  $request->validate([
    //         'phone' => 'sometimes|string|max:15',
    //         'address' => 'sometimes|string|max:255',
    //         'cv' => 'sometimes|file|mimes:pdf,doc,docx,xlsx,txt|max:2048',
    //         'photograph' => 'sometimes|image|mimes:jpeg,png,jpg,gif|max:2048',
    //     ]);
    //     // $medicalProfile = MedicalProfile::findOrFail($id)
    //     // // Verificación de ID en la solicitud
    //     // $profileId = $request->id ?? $request->route('id'); // Usa el ID desde el cuerpo o la URL

    //     // if (!$profileId) {
    //     //     return response()->json(['error' => 'ID del perfil médico no proporcionado'], 400);
    //     // }

    //     // // Obtiene el perfil médico existente
    //     // $medicalProfile = MedicalProfile::find($profileId);

    //     // if (!$medicalProfile) {
    //     //     return response()->json(['error' => 'Perfil médico no encontrado'], 404);
    //     // }
    //     $medicalProfile = MedicalProfile::findOrFail($id);
    //     // Actualiza los campos del perfil médico
    //     $medicalProfile->phone = $data['phone'];
    //     $medicalProfile->address = $data['address'];

    //     // Manejo de archivo CV
    //     if ($request->hasFile('cv')) {

    //         $cv_path = Storage::put('upload', $request->file('cv'));
    //         $data['cv'] = $cv_path;
    //         // Almacena el archivo y guarda la ruta en el modelo
    //         // $medicalProfile->cv = $request->file('cv')->store('cvs', 'public');
    //     }

    //     // Manejo de archivo de fotografía
    //     if ($request->hasFile('photograph')) {

    //         $photograph_path = Storage::put('upload', $request->file('photograph'));
    //         $data['photograph'] = $photograph_path;
    //         // Almacena el archivo y guarda la ruta en el modelo
    //         // $medicalProfile->photograph = $request->file('photograph')->store('photographs', 'public');
    //     }

    //     // Guarda los cambios
    //     $medicalProfile->save();
        
    //     return response()->json(['status' => 'success', 'message' => 'Perfil médico actualizado exitosamente.']);
    //     dd($request->all());
    // }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    public function destroy( $id)
    {
        try {
            $medicalProfile = MedicalProfile::findOrFail($id);
            $medicalProfile->delete();

            return response()->json(['status' => 'success', 'message' => 'Perfil médico eliminado correctamente.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Error al eliminar el perfil médico.'], 500);
        }
    }

    public function profilo()
    {
        // Obtener los perfiles médicos del usuario autenticado
        $userId = auth()->id();
        $profiles = MedicalProfile::where('user_id', $userId)->paginate(100);

        return response()->json([
            'status' => 'success',
            'results' => $profiles,
        ]);
    }
    public function showProfile( $slug, $id)
    {
        // Obtener los perfiles médicos del usuario autenticado
        $medical_profile = MedicalProfile::where('slug', $slug)
            ->where('id', $id)
            ->with('reviews', 'user', 'medicalspecializations', 'payments', 'messages', 'statistics')
            ->first();

        if ($medical_profile) {
            return response()->json([
                'status' => 'success',
                'results' => $medical_profile
            ]);
        } else {
            return response()->json([
                'status' => 'failed',
                'results' => null
            ], 404); // Añadir el manejo de error
        } 
    }

}
