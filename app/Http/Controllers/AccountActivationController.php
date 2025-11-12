<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Carbon\Carbon;

class AccountActivationController extends Controller
{
    /**
     * Afficher le formulaire d'activation de compte
     */
    public function showActivationForm($token)
    {
        // Vérifier si le token existe
        $activation = DB::table('account_activations')
            ->where('token', function($query) use ($token) {
                // Le token en base est hashé, on doit le vérifier différemment
            })
            ->first();

        return view('auth.activation-du-compte', [
            'token' => $token
        ]);
    }

    /**
     * Activer le compte et définir le mot de passe
     */
    public function activate(Request $request)
    {
        // Validation
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ], [
            'email.required' => 'L\'email est obligatoire',
            'email.email' => 'Format d\'email invalide',
            'password.required' => 'Le mot de passe est obligatoire',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères',
            'password.confirmed' => 'Les mots de passe ne correspondent pas',
        ]);

        try {
            // Récupérer tous les tokens pour cet email
            $activations = DB::table('account_activations')
                ->where('email', $request->email)
                ->get();

            if ($activations->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce lien d\'activation est invalide ou a déjà été utilisé.'
                ], 400);
            }

            // Vérifier si un des tokens correspond
            $validActivation = null;
            foreach ($activations as $activation) {
                if (Hash::check($request->token, $activation->token)) {
                    $validActivation = $activation;
                    break;
                }
            }

            if (!$validActivation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce lien d\'activation est invalide.'
                ], 400);
            }

            // Vérifier si le token n'a pas expiré (48 heures)
            $createdAt = Carbon::parse($validActivation->created_at);
            if ($createdAt->addHours(48)->isPast()) {
                DB::table('account_activations')->where('email', $request->email)->delete();

                return response()->json([
                    'success' => false,
                    'message' => 'Ce lien d\'activation a expiré. Veuillez contacter l\'administrateur.'
                ], 400);
            }

            // Trouver l'utilisateur
            $user = User::where('email', $request->email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Utilisateur non trouvé.'
                ], 404);
            }

            // Vérifier si le compte est déjà activé
            if ($user->actif) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce compte est déjà activé. Vous pouvez vous connecter.'
                ], 400);
            }

            // Activer le compte et définir le mot de passe
            $user->password = Hash::make($request->password);
            $user->actif = true;
            $user->save();

            // Supprimer le token d'activation
            DB::table('account_activations')->where('email', $request->email)->delete();

            Log::info('Compte activé avec succès', [
                'user_id' => $user->id_user,
                'email' => $user->email
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Votre compte a été activé avec succès ! Vous pouvez maintenant vous connecter.',
                'redirect' => route('index')
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'activation du compte', [
                'error' => $e->getMessage(),
                'email' => $request->email ?? 'N/A'
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite lors de l\'activation de votre compte.'
            ], 500);
        }
    }

    /**
     * Vérifier la validité d'un token
     */
    public function verifyToken(Request $request)
    {
        $token = $request->token;
        $email = $request->email;

        try {
            $activations = DB::table('account_activations')
                ->where('email', $email)
                ->get();

            if ($activations->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'valid' => false,
                    'message' => 'Token invalide ou expiré'
                ]);
            }

            // Vérifier si un des tokens correspond
            $validActivation = null;
            foreach ($activations as $activation) {
                if (Hash::check($token, $activation->token)) {
                    $validActivation = $activation;
                    break;
                }
            }

            if (!$validActivation) {
                return response()->json([
                    'success' => false,
                    'valid' => false,
                    'message' => 'Token invalide'
                ]);
            }

            // Vérifier l'expiration
            $createdAt = Carbon::parse($validActivation->created_at);
            if ($createdAt->addHours(48)->isPast()) {
                return response()->json([
                    'success' => false,
                    'valid' => false,
                    'message' => 'Token expiré'
                ]);
            }

            // Récupérer l'utilisateur
            $user = User::where('email', $email)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'valid' => false,
                    'message' => 'Utilisateur non trouvé'
                ]);
            }

            return response()->json([
                'success' => true,
                'valid' => true,
                'user' => [
                    'nom' => $user->nom,
                    'prenom' => $user->prenom,
                    'email' => $user->email
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur vérification token', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'valid' => false,
                'message' => 'Erreur lors de la vérification'
            ], 500);
        }
    }
}
