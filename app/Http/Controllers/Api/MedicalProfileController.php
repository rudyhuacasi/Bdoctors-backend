<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicalProfile;
use App\Models\MedicinePerformance;
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
            'specialization_id' => 'required|exists:specializations,id',
            'performances' => 'required|array',  
            'performances.*' => 'integer|exists:medicine_performances,id'       
        ]);

        $data = $request->all();
        $medicalProfile = new MedicalProfile();

        $user = User::find(auth()->id());

        // ID dell'usuario
        $medicalProfile->user_id = $user->id;

        // dati per la tabella
        $firstName = $user->name;
        $lastName = $user->last_name;
        $prefix = $data['gender'] === 'Maschio' ? 'dr' : 'dra';

        $medicalProfile->slug = $prefix . '-' . Str::slug($firstName . '-' . $lastName);        
        $medicalProfile->phone = $data['phone'];
        $medicalProfile->specialization_id = $data['specialization_id'];
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

        $medicalProfile->performances()->sync($request->performances);

        return response()->json(['status' => 'success', 'message' => 'Profilo medico fatto.']);
    }

    public function indexPerformance()
    {
        try {
            $performances = MedicinePerformance::all();
            return response()->json($performances, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al obtener las prestaciones'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show( $slug, $id)
    {
        //? dettaglio con relazione services:
        $medical_profile = MedicalProfile::where('slug', $slug)
            ->where('id', $id)
            ->with('reviews',
            'user',
            'payments',
            'messages',
            'statistics',
            'specializations',
            'profilePerformances.medicinePerformance')
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
        // Validazione
        $request->validate([
            'phone' => 'sometimes|string|max:15',
            'address' => 'sometimes|string|max:255',
            'cv' => 'sometimes|string',
            'photograph' => 'sometimes|string',
            'specialization_id' => 'sometimes|exists:specializations,id',
            'performances' => 'sometimes|array',
            'performances.*' => 'integer|exists:medicine_performances,id',
        ]);

        $medicalProfile = MedicalProfile::findOrFail($id);
        if (!$medicalProfile) {
             return response()->json(['error' => 'Usuario non trovato'], 404);
        }

        if ($request->has('phone')) {
            $medicalProfile->phone = $request->input('phone');
        }

        if ($request->has('address')) {
            $medicalProfile->address = $request->input('address');
        }

        if ($request->has('specialization_id')) {
            $medicalProfile->specialization_id = $request->input('specialization_id');
        }

        if ($request->has('cv')) {
            $cvData = $request->input('cv');

            $filePath = 'cvs/' . uniqid() . '.pdf';
            Storage::disk('public')->put($filePath, base64_decode($cvData));
            $medicalProfile->cv = $filePath;
        }

        if ($request->has('photograph')) {
            $photoData = $request->input('photograph');
            $photoPath = 'photographs/' . uniqid() . '.png';
            Storage::disk('public')->put($photoPath, base64_decode($photoData));
            $medicalProfile->photograph = $photoPath;
        }
        $medicalProfile->save();

        if ($request->has('performances')) {
            $medicalProfile->performances()->sync($request->performances);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Profilo medico aggiornato',
            'results' => $medicalProfile->load('performances', 'specializations') 
        ]);
        
    }

    // /**
    //  * Remove the specified resource from storage.
    //  */
    public function destroy( $id)
    {
        try {
            $medicalProfile = MedicalProfile::findOrFail($id);
            $medicalProfile->delete();

            return response()->json(['status' => 'success', 'message' => 'Profilo medico cancellato.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Errore a cancellare il profilo.'], 500);
        }
    }

    public function profilo()
    {
        $userId = auth()->id();

        $profilesWithPayments = MedicalProfile::where('user_id', $userId)
            ->whereHas('payments')       
            ->with(['payments.sponsorship']) 
            ->inRandomOrder()             
            ->get();                      

        $profilesWithoutPayments = MedicalProfile::where('user_id', $userId)
            ->whereDoesntHave('payments') 
            ->inRandomOrder()             
            ->get();                      

        $combinedProfiles = $profilesWithPayments->merge($profilesWithoutPayments);

        return response()->json([
            'status' => 'success',
            'results' => $combinedProfiles, 
        ]);
    }
    public function showProfile( $slug, $id)
    {
        $medical_profile = MedicalProfile::where('slug', $slug)
            ->where('id', $id)
            ->with('reviews',
            'user',
            'payments',
            'messages',
            'statistics',
            'specializations',
            'profilePerformances.medicinePerformance')
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

    public function search(Request $request)
    {
        $query = MedicalProfile::query()
            ->with(['specializations', 'reviews', 'statistics']);

        if ($request->filled('specialization')) {
            $query->whereHas('specializations', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('specialization') . '%');
            });
        }

        if ($request->filled('min_rating')) {
            $query->whereHas('statistics', function ($q) use ($request) {
                $q->where('media', '>=', $request->input('min_rating'));
            });
        }

        if ($request->filled('min_reviews')) {
            $query->whereHas('statistics', function ($q) use ($request) {
                $q->where('reviews_received', '>=', $request->input('min_reviews'));
            });
        }

        $medicalProfiles = $query->get();

        return response()->json($medicalProfiles);
    }
    public function searchSpecializations(Request $request)
    {
        $term = $request->query('term');
        $specializations = Specialization::where('name', 'LIKE', "%{$term}%")->get();
        return response()->json($specializations);
    }
}
