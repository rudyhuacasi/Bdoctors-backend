<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicalProfile;
use App\Models\Specialization;
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
        $medical_profile = MedicalProfile::inRandomOrder()->paginate(9);

        return response()->json([
            'status' => 'success',
            'results' => $medical_profile,
        ]);
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
            'gender' => 'required|string|in:Maschio,Femminile',
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
        $prefix = $data['gender'] === 'Maschio' ? 'dr' : 'dra';

        $medicalProfile->slug = $prefix . '-' . Str::slug($firstName . '-' . $lastName);        
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
        // Obtener el ID del usuario autenticado
        $userId = auth()->id();

        // Perfiles con payments
        $profilesWithPayments = MedicalProfile::where('user_id', $userId)
            ->whereHas('payments')       // Solo perfiles con pagos
            ->with(['payments.sponsorship']) // Cargar relaciones de payments y sponsorship
            ->inRandomOrder()             // Orden aleatorio dentro de esta categoría
            ->get();                      // Obtener todos los resultados

        // Perfiles sin payments
        $profilesWithoutPayments = MedicalProfile::where('user_id', $userId)
            ->whereDoesntHave('payments') // Solo perfiles sin pagos
            ->inRandomOrder()              // Orden aleatorio dentro de esta categoría
            ->get();                      // Obtener todos los resultados

        // Combinar ambos conjuntos
        $combinedProfiles = $profilesWithPayments->merge($profilesWithoutPayments);

        return response()->json([
            'status' => 'success',
            'results' => $combinedProfiles, // Devolver todos los perfiles combinados
        ]);
    }
    public function showProfile( $slug, $id)
    {
        // Obtener los perfiles médicos del usuario autenticado
        $medical_profile = MedicalProfile::where('slug', $slug)
            ->where('id', $id)
            ->with('reviews', 'user', 'medicalspecializations', 'payments', 'messages','statistics')
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

    public function search(Request $request)
    {
        $query = MedicalProfile::query()
            ->with(['specializations', 'reviews', 'statistics']);

        // Filtrar por especialización
        if ($request->filled('specialization')) {
            $query->whereHas('specializations', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('specialization') . '%');
            });
        }

        // Filtrar por calificación media mínima
        if ($request->filled('min_rating')) {
            $query->whereHas('statistics', function ($q) use ($request) {
                $q->where('average_rating', '>=', $request->input('min_rating'));
            });
        }

        // Filtrar por número mínimo de reseñas
        if ($request->filled('min_reviews')) {
            $query->whereHas('statistics', function ($q) use ($request) {
                $q->where('reviews_received', '>=', $request->input('min_reviews'));
            });
        }

        // Obtener los resultados
        $medicalProfiles = $query->get();

        // Retornar los resultados como JSON
        return response()->json($medicalProfiles);
    }
    public function searchSpecializations(Request $request)
    {
        $term = $request->query('term');
        $specializations = Specialization::where('name', 'LIKE', "%{$term}%")->get();
        return response()->json($specializations);
    }
}
