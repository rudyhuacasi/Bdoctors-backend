<?php

namespace App\Http\Controllers\Api;

use App\Models\Sponsorship;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MedicalProfile;
use Braintree\Gateway;

class SponsorshipController extends Controller
{
    // Método para generar el token de cliente
    public function generateToken()
    {
        // Configura la conexión con Braintree usando las credenciales de tu archivo .env
        $gateway = new Gateway([
            'environment' => env('BRAINTREE_ENV'),
            'merchantId' => env('BRAINTREE_MERCHANT_ID'),
            'publicKey' => env('BRAINTREE_PUBLIC_KEY'),
            'privateKey' => env('BRAINTREE_PRIVATE_KEY')
        ]);

        // Genera el token de cliente para la sesión
        $clientToken = $gateway->clientToken()->generate();

        // Retorna el token de cliente en un JSON
        return response()->json([
            'clientToken' => $clientToken
        ]);
    }

    public function processPayment(Request $request)
    {
        // Validar los datos recibidos
        $request->validate([
            'package_id' => 'required|integer',
            'payment_method_nonce' => 'required|string',
            'medical_profile_id' => 'required|integer'
        ]);

        $gateway = new Gateway([
            'environment' => env('BRAINTREE_ENV'),
            'merchantId' => env('BRAINTREE_MERCHANT_ID'),
            'publicKey' => env('BRAINTREE_PUBLIC_KEY'),
            'privateKey' => env('BRAINTREE_PRIVATE_KEY')
        ]);

        // Buscar el paquete en la base de datos
        $sponsorship = Sponsorship::find($request->package_id);

        // Verificar si el paquete existe
        if (!$sponsorship) {
            return response()->json(['error' => 'Paquete inválido'], 400);
        }

        // Procesar la transacción con el precio del paquete desde la base de datos
        $result = $gateway->transaction()->sale([
            'amount' => $sponsorship->price,
            'paymentMethodNonce' => $request->payment_method_nonce,
            'options' => [
                'submitForSettlement' => true
            ]
        ]);

        if ($result->success) {
            // Calcular las fechas de inicio y fin del paquete
            $startDate = now();
            $endDate = $startDate->copy()->addHours($this->getDurationInHours($sponsorship->package));

            // Crear un nuevo registro en la tabla payments
            \App\Models\Payment::create([
                'medical_profile_id' => $request->medical_profile_id,
                'sponsorship_id' => $sponsorship->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'state' => 'active',
            ]);

            return response()->json([
                'success' => true,
                'transaction' => $result->transaction
            ]);
        } else {
            return response()->json([
                'error' => 'Pago fallido',
                'message' => $result->message
            ], 500);
        }
    }

    private function getDurationInHours($packageName)
    {
        switch ($packageName) {
            case '24 ore':
                return 24;
            case '72 ore':
                return 72;
            case '144 ore':
                return 144;
            default:
                return 0; 
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sponsorships = Sponsorship::all();

        return response()->json([
            'status' => 'success',
            'results' => $sponsorships,
        ]);
    }

    public function indexUser()
    {
        // Filtrar solo los perfiles con patrocinio
        $sponsoredProfiles = MedicalProfile::whereHas('payments')->get();

        return response()->json([
            'status' => 'success',
            'results' => $sponsoredProfiles,
        ]);
    }
    
    
}
