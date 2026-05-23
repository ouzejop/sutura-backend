<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Patient;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function proLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Identifiants incorrects'], 401);
        }

        $token = $user->createToken('pro-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function patientRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pseudo' => 'required|string|unique:patients',
            'age' => 'required|integer',
            'sex' => 'required|in:male,female',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $patient = Patient::create([
            'pseudo' => $request->pseudo,
            'age' => $request->age,
            'sex' => $request->sex,
            'avatar' => $request->avatar ?? null,
        ]);

        $token = $patient->createToken('patient-token')->plainTextToken;

        return response()->json([
            'patient' => $patient,
            'token' => $token,
        ], 201);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Déconnecté avec succès.']);
    }

    public function updateSecureData(Request $request)
    {
        $patient = $request->user();
        if (!$patient instanceof Patient) {
            return response()->json(['message' => 'Action non autorisée.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'phone' => 'nullable|string|max:20',
            'emergency_contact' => 'nullable|string|max:20',
            'real_name' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $patient->update([
            'phone' => $request->phone,
            'emergency_contact' => $request->emergency_contact,
            'real_name' => $request->real_name,
        ]);

        return response()->json(['message' => 'Données sécurisées mises à jour.']);
    }
}
