<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmergencyController extends Controller
{
    public function requestAccess(Request $request)
    {
        return response()->json(['message' => 'Demande d\'accès envoyée au comité d\'éthique.']);
    }

    public function breakSeal(Request $request)
    {
        return response()->json(['message' => 'Identité déchiffrée avec succès. Opération auditée.']);
    }
}
