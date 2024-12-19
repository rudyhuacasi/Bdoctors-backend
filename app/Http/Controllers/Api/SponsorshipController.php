<?php

namespace App\Http\Controllers\Api;

use App\Models\Sponsorship;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MedicalProfile;
use App\Models\Payment;
use Braintree\Gateway;
use Carbon\Carbon;

class SponsorshipController extends Controller
{
    public function generateToken()
    {
        $gateway = new Gateway([
            'environment' => env('BRAINTREE_ENV'),
            'merchantId' => env('BRAINTREE_MERCHANT_ID'),
            'publicKey' => env('BRAINTREE_PUBLIC_KEY'),
            'privateKey' => env('BRAINTREE_PRIVATE_KEY')
        ]);

        $clientToken = $gateway->clientToken()->generate();

        return response()->json([
            'clientToken' => $clientToken
        ]);
    }

    public function processPayment(Request $request)
    {
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

        $sponsorship = Sponsorship::find($request->package_id);

        if (!$sponsorship) {
            return response()->json(['error' => 'no hai scelto un pacco '], 400);
        }

        $result = $gateway->transaction()->sale([
            'amount' => $sponsorship->price,
            'paymentMethodNonce' => $request->payment_method_nonce,
            'options' => [
                'submitForSettlement' => true
            ]
        ]);

        if ($result->success) {
            $startDate = now();
            $endDate = $startDate->copy()->addHours($this->getDurationInHours($sponsorship->package));

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
        $now = now();

        $payments = Payment::where('start_date', '<=', $now)
        ->where('end_date', '>=', $now)
        ->with('medicalProfile') 
        ->get();

        $groupedByProfile = $payments->groupBy('medical_profile_id');

        $results = $groupedByProfile->map(function ($sponsorships, $medicalProfileId) {
            $totalActiveHours = 0;

            foreach ($sponsorships as $sponsorship) {
                $start = \Carbon\Carbon::parse($sponsorship->start_date);
                $end = \Carbon\Carbon::parse($sponsorship->end_date);

                $totalActiveHours += $start->diffInHours($end);
            }

            return [
                'medical_profile' => $sponsorships->first()->medicalProfile,
                'total_active_hours' => $totalActiveHours,
            ];
        })
        ->filter(function ($result) {
            return $result['total_active_hours'] > 0;
        })
        ->values();

        return response()->json([
            'results' => $results
        ], 200);
    }
    
    
}
