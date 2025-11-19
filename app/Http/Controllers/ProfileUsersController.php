<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class ProfileUsersController extends Controller
{
    /**
     * Afficher la page de profil selon le rôle
     */
    public function index()
    {
        $user = Auth::user();

        // Rediriger vers la vue appropriée selon le rôle
        if ($user->isAdmin()) {
            return view('admin.profile', compact('user'));
        } elseif ($user->isChefDepartement()) {
            return view('chef-de-departement.profile', compact('user'));
        } else {
            return view('employes.profile', compact('user'));
        }
    }

    /**
     * Récupérer les informations du profil
     */
    public function getProfile()
    {
        try {
            $user = Auth::user()->load(['role', 'departement']);

            // ✅ CORRECTION : Nettoyer et générer l'URL complète de la photo
            $photoUrl = null;
            if ($user->photo_url) {
                // Nettoyer le chemin (enlever les backslashes et préfixes incorrects)
                $cleanPath = str_replace('\\', '/', $user->photo_url);
                $cleanPath = preg_replace('/^storage\/app\/public\//', '', $cleanPath);

                if (Storage::disk('public')->exists($cleanPath)) {
                    $photoUrl = asset('storage/' . $cleanPath);
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id_user' => $user->id_user,
                    'nom' => $user->nom,
                    'prenom' => $user->prenom,
                    'email' => $user->email,
                    'telephone' => $user->telephone,
                    'profession' => $user->profession,
                    'matricule' => $user->matricule,
                    'date_embauche' => $user->date_embauche->format('Y-m-d'),
                    'photo_url' => $photoUrl,
                    'role' => $user->role ? $user->role->nom_role : null,
                    'departement' => $user->departement ? $user->departement->nom_departement : null,
                    'solde_conges_annuel' => $user->solde_conges_annuel,
                    'conges_pris' => $user->conges_pris,
                    'conges_restants' => $user->conges_restants,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération du profil: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du profil'
            ], 500);
        }
    }

    /**
     * Mettre à jour les informations du profil
     */
    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();

            // Validation
            $validator = Validator::make($request->all(), [
                'nom' => 'required|string|max:100',
                'prenom' => 'required|string|max:100',
                'email' => 'required|email|unique:users,email,' . $user->id_user . ',id_user',
                'telephone' => 'nullable|string|max:20',
            ], [
                'nom.required' => 'Le nom est requis',
                'prenom.required' => 'Le prénom est requis',
                'email.required' => 'L\'email est requis',
                'email.email' => 'L\'email doit être valide',
                'email.unique' => 'Cet email est déjà utilisé',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            // Mettre à jour les informations
            $user->update([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'telephone' => $request->telephone,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Profil mis à jour avec succès',
                'data' => $user
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du profil: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du profil'
            ], 500);
        }
    }

    /**
     * Mettre à jour la photo de profil
     */
 public function updatePhoto(Request $request)
{
    try {
        $user = Auth::user();

        // Validation
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ], [
            'photo.required' => 'Veuillez sélectionner une photo',
            'photo.image' => 'Le fichier doit être une image',
            'photo.mimes' => 'L\'image doit être au format jpeg, png, jpg ou gif',
            'photo.max' => 'L\'image ne doit pas dépasser 2MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        // Supprimer l'ancienne photo si elle existe
        if ($user->photo_url) {
            $oldPath = str_replace('\\', '/', $user->photo_url);
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        // Enregistrer la nouvelle photo
        $photo = $request->file('photo');
        $filename = time() . '_' . $user->matricule . '.' . $photo->getClientOriginalExtension();

        // ✅ IMPORTANT : Utiliser putFileAs au lieu de storeAs pour éviter les problèmes de chemin
        Storage::disk('public')->putFileAs(
            'uploads/profile',
            $photo,
            $filename
        );

        // ✅ Construire le chemin avec des slashes normaux
        $path = 'uploads/profile/' . $filename;

        // Mettre à jour l'utilisateur
        $user->update([
            'photo_url' => $path
        ]);

        $photoUrl = asset('storage/' . $path);

        return response()->json([
            'success' => true,
            'message' => 'Photo de profil mise à jour avec succès',
            'photo_url' => $photoUrl
        ]);

    } catch (\Exception $e) {
        Log::error('Erreur lors de la mise à jour de la photo: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la mise à jour de la photo'
        ], 500);
    }
}
    /**
     * Supprimer la photo de profil
     */
    public function deletePhoto()
    {
        try {
            $user = Auth::user();

            // ✅ CORRECTION : Nettoyer le chemin avant suppression
            if ($user->photo_url) {
                $cleanPath = str_replace('\\', '/', $user->photo_url);
                $cleanPath = preg_replace('/^storage\/app\/public\//', '', $cleanPath);

                if (Storage::disk('public')->exists($cleanPath)) {
                    Storage::disk('public')->delete($cleanPath);
                }
            }

            $user->update([
                'photo_url' => null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Photo de profil supprimée avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la photo: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de la photo'
            ], 500);
        }
    }

    /**
     * Changer le mot de passe
     */
    public function updatePassword(Request $request)
    {
        try {
            $user = Auth::user();

            // Validation
            $validator = Validator::make($request->all(), [
                'current_password' => 'required',
                'new_password' => 'required|min:8|confirmed',
            ], [
                'current_password.required' => 'Le mot de passe actuel est requis',
                'new_password.required' => 'Le nouveau mot de passe est requis',
                'new_password.min' => 'Le nouveau mot de passe doit contenir au moins 8 caractères',
                'new_password.confirmed' => 'La confirmation du mot de passe ne correspond pas',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            // Vérifier le mot de passe actuel
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le mot de passe actuel est incorrect'
                ], 422);
            }

            // Vérifier que le nouveau mot de passe est différent de l'ancien
            if (Hash::check($request->new_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le nouveau mot de passe doit être différent de l\'ancien'
                ], 422);
            }

            // Mettre à jour le mot de passe
            $user->update([
                'password' => Hash::make($request->new_password)
            ]);

            // Déconnecter l'utilisateur
            Auth::logout();

            // Invalider la session
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'success' => true,
                'message' => 'Mot de passe modifié avec succès. Vous allez être redirigé vers la page de connexion.',
                'redirect' => route('index') // Retourner l'URL de redirection
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors du changement de mot de passe: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du changement de mot de passe'
            ], 500);
        }
    }
}
