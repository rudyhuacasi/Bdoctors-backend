<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Registro de nuevos usuarios
    public function register(RegisterRequest $request)
    {

        $user = User::create([
            'name' => $request->name,  // nome in italiano -> first_name in inglese
            'last_name' => $request->last_name,  // cognome in italiano -> last_name in inglese
            'address' => $request->address,  // indirizzo in italiano -> address in inglese
            'specialization' => $request->specialization,  // specializzazione -> specialization
            'email' => $request->email,  // email rimane invariato
            'password' => Hash::make($request->password),  // password rimane invariato
        ]);



        $token = $user->createToken('MyApp')->plainTextToken;

        return response()->json(['token' => $token], 201);
    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Intentar autenticar al usuario usando email y password
        if (!Auth::attempt(['email' => $validatedData['email'], 'password' => $validatedData['password']])) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Obtener el usuario autenticado
        $user = Auth::user();

        // Generar un token para el usuario autenticado
        $token = $user->createToken('MyApp')->plainTextToken;

        return response()->json(['token' => $token], 200);
    }
}