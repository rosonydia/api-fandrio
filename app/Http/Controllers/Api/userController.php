<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Api\Utilisateur;

class UserController extends Controller
{
    public function store(Request $request) {

        $validated = $request->validate([
            'nom' => 'required|string',
            'prenom' => 'required|string',
            'fonction' => 'required|string',
        ]);

        $utilisateur = Utilisateur::create([
            'nom' => $validated['nom'],
            'prenom' => $validated['prenom'],
            'fonction' => $validated['fonction'],
        ]);

        return response()->json(['message' => 'Utilisateur créé', 'utilisateur' => $utilisateur], 201);
    }
}
