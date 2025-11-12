<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AdminDashboardController extends Controller
{
    /**
     * Afficher le tableau de bord admin
     */
    public function index()
    {
        $user = Auth::user();

        // Statistiques générales
        $stats = [
            'total_employes' => User::where('actif', true)->count(),
            'total_departements' => \DB::table('departements')->count(),
            'total_chefs' => User::whereHas('role', function($query) {
                $query->where('nom_role', 'chef de departement');
            })->where('actif', true)->count(),
            'demandes_attente' => 12, // À remplacer par votre logique
        ];

        return view('admin.dashboard-admin', compact('user', 'stats'));
    }

    /**
     * Afficher le profil admin
     */
    public function profile()
    {
        $user = Auth::user();
        return view('admin.profile', compact('user'));
    }

    /**
     * Mettre à jour les informations du profil
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'prenom' => 'required|string|max:255',
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id_user . ',id_user',
            'telephone' => 'nullable|string|max:20',
        ], [
            'prenom.required' => 'Le prénom est obligatoire',
            'nom.required' => 'Le nom est obligatoire',
            'email.required' => 'L\'email est obligatoire',
            'email.unique' => 'Cet email est déjà utilisé',
        ]);

        try {
            $user->prenom = $request->prenom;
            $user->nom = $request->nom;
            $user->email = $request->email;
            $user->telephone = $request->telephone;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Profil mis à jour avec succès',
                'user' => [
                    'prenom' => $user->prenom,
                    'nom' => $user->nom,
                    'email' => $user->email,
                    'telephone' => $user->telephone,
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la mise à jour du profil', [
                'user_id' => $user->id_user,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite lors de la mise à jour'
            ], 500);
        }
    }

    /**
     * Uploader la photo de profil
     */
    public function uploadPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ], [
            'photo.required' => 'Veuillez sélectionner une photo',
            'photo.image' => 'Le fichier doit être une image',
            'photo.mimes' => 'Formats acceptés : jpeg, png, jpg, gif',
            'photo.max' => 'La taille maximale est de 2MB',
        ]);

        try {
            $user = Auth::user();

            // Supprimer l'ancienne photo si elle existe
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            // Stocker la nouvelle photo
            $photoPath = $request->file('photo')->store('profiles', 'public');

            // Mettre à jour le chemin dans la base de données
            $user->photo = $photoPath;
            $user->save();

            Log::info('Photo de profil mise à jour', [
                'user_id' => $user->id_user,
                'photo_path' => $photoPath
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Photo de profil mise à jour avec succès',
                'photo_url' => asset('storage/' . $photoPath)
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'upload de la photo', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite lors de l\'upload de la photo'
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

            // Supprimer la photo si elle existe
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            // Mettre à jour la base de données
            $user->photo = null;
            $user->save();

            Log::info('Photo de profil supprimée', [
                'user_id' => $user->id_user
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Photo de profil supprimée avec succès'
            ], 200);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression de la photo', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur s\'est produite lors de la suppression'
            ], 500);
        }
    }
}
