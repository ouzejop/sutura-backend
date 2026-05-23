<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pass;

class PassController extends Controller
{
    public function generate(Request $request)
    {
        // MVP: Simulate Pass Generation
        return response()->json([
            'pass' => [
                'id' => uniqid(),
                'product' => $request->product ?? 'Préservatifs x12',
                'status' => 'issued',
                'payload' => base64_encode(json_encode(['product' => 'Préservatifs x12', 'exp' => time() + 86400]))
            ]
        ], 201);
    }

    public function myPasses(Request $request)
    {
        return response()->json(['passes' => []]);
    }

    public function verify(Request $request)
    {
        return response()->json(['message' => 'Pass valide.']);
    }

    public function consume(Request $request)
    {
        return response()->json(['message' => 'Produit délivré avec succès.']);
    }
}
