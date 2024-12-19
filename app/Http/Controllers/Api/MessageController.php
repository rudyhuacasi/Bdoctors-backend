<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Http\Requests\StoreMessageRequest;
use App\Http\Requests\UpdateMessageRequest;
use App\Models\MedicalProfile;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
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
    public function store(Request $request, Message $message, $id)
    {
        $validatedData = $request->validate([
            'email_sender' => 'required|email',
            'content' => 'required|string|max:500',
            'medical_profile_id' => 'required|exists:medical_profiles,id',
        ]);
        $medical_profile = MedicalProfile::where('id', $id)->first();

        if (!$medical_profile) {
            return response()->json(['error' => 'Apartamento no encontrado.'], 404);
        }

        $message->email_sender = $validatedData['email_sender'];
        $message->content = $validatedData['content'];
        $message->medical_profile_id = $medical_profile->id;
        $message->save();

        return response()->json(['message' => 'Mensaje guardado exitosamente'], 201);
    
    }

    /**
     * Display the specified resource.
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Message $message, $id)
    {
    
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Message $message)
    {
        //
    }
}
