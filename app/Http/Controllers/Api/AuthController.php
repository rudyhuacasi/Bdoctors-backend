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
        // Validar los datos de entrada
        $validatedData = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Intentar autenticar al usuario
        if (!Auth::attempt(['email' => $validatedData['email'], 'password' => $validatedData['password']])) {
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // Obtener el usuario autenticado
        $user = Auth::user();

        // Verificar si el usuario autenticado es una instancia del modelo User
        if (!$user instanceof \App\Models\User) {
            return response()->json(['error' => 'Usuario no autenticado correctamente'], 401);
        }

        // Crear el token si la instancia es válida
        $token = $user->createToken('MyApp')->plainTextToken;

        // Retornar la respuesta con el token generado
        return response()->json(['token' => $token], 200);
    }

}