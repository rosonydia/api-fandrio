<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Carbon;

class AuthController extends Controller
{
    // Fonction d'inscription sur l'application
    public function register(Request $request) {
       
        // Validation des données entrant
        try {
            $request->validate([
                'user_name' => 'required|string|max:255',
                'user_mail' => 'required|email|unique:users,user_mail',
                'user_password' => 'required|string|min:6|confirmed',
                'user_phone' => 'sometimes|string|max:20'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return $this->ApiResponse(422, null, json_encode($e->errors()));
        }

        // Envoie des données utilisateur vers le models User pour enregistrement
        $user = User::create([
            'user_name' => $request->user_name,
            'user_mail' => $request->user_mail,
            'user_password' => Hash::make($request->user_password),
            'user_phone'    => $request->user_phone,
            'role_id'   => 2, // Consomateur (utilisateur de l'application)
            'insert_date'   => Carbon::now(),
            'update_date'   => Carbon::now()
        ]);

        // Création du token utilisateur
        $token = $user->createToken('authToken')->plainTextToken;
        // return response()->json(['token' => $token, 'user' => $user]);
        return $this->ApiResponse(0, $user, 'Utilisateur inscrit avec succès');
    }


    // Fonction de connexion sur l'application
    public function login(Request $request) {

        // Validation des données envoyés par flutter
        try {
            $request->validate([
                'user_mail' => 'required|email',
                'user_password' => 'required|string'
            ]);
        }catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreurs de validation',
                'errors' => $e->errors()
            ], 422);
        }

        // Check de l'email utilisateur dans la base
        $user = User::where('user_mail', $request->user_mail)->first();

        // Si identifiant incorrecte
        if (!$user) {
            return response()->json(['message' => 'Identifiants invalides'], 401);
        }

        // Si mot de passe incorrecte
        if (!Hash::check($request->user_password, $user->user_password)) {
            return response()->json(['message' => 'Mot de passe incorrect'], 401);
        }

        // Vérifie si l'utilisateur est bien un consommateur
        if($user->role_id != 2) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $token = $user->createToken('authToken')->plainTextToken;
        return response()->json(['token' => $token, 'user' => $user]);
    }


    // Fonction me
    public function me(Request $request) {
        return response()->json($request->user());
    }

    // Fonction de déconnexion
    public function logout(Request $request) {
        
        // Suppression du token 
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Deconnexion réussie']);
    }

}
