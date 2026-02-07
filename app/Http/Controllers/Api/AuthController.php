<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * AuthController
 * Gère l'authentification de la direction via Laravel Sanctum
 */
class AuthController extends Controller
{
    /**
     * LOGIN - Connexion de la direction
     *
     * Workflow:
     * 1. Validation des données (email + password requis)
     * 2. Vérification que l'utilisateur existe
     * 3. Vérification du mot de passe
     * 4. Vérification que le compte est actif
     * 5. Génération d'un token Sanctum
     * 6. Retour du token + infos utilisateur
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        // 1. Validation des données entrantes
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 2. Recherche de l'utilisateur par email
        $user = User::where('email', $request->email)->first();

        // 3. Vérifications de sécurité
        // - L'utilisateur existe ?
        // - Le mot de passe est correct ?
        // - Le compte est actif ?
        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Les identifiants sont incorrects.'],
            ]);
        }

        if (!$user->actif) {
            throw ValidationException::withMessages([
                'email' => ['Ce compte a été désactivé.'],
            ]);
        }

        // 4. Suppression des anciens tokens (optionnel : permet de n'avoir qu'une seule session active)
        // $user->tokens()->delete();

        // 5. Génération d'un nouveau token Sanctum
        // Le token est stocké dans la table 'personal_access_tokens'
        $token = $user->createToken('auth_token')->plainTextToken;

        // 6. Retour du token et des infos utilisateur
        return response()->json([
            'message' => 'Connexion réussie',
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ],
        ], 200);
    }

    /**
     * LOGOUT - Déconnexion de la direction
     *
     * Workflow:
     * 1. Récupération de l'utilisateur connecté (injecté par le middleware auth:sanctum)
     * 2. Suppression du token actuel
     * 3. Confirmation de déconnexion
     *
     * Note: Cette route doit être protégée par le middleware 'auth:sanctum'
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        // 1. Suppression du token actuel de l'utilisateur connecté
        // $request->user() existe grâce au middleware 'auth:sanctum'
        // currentAccessToken() récupère le token utilisé pour cette requête
        $request->user()->currentAccessToken()->delete();

        // 2. Confirmation
        return response()->json([
            'message' => 'Déconnexion réussie',
        ], 200);
    }
}
