<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Role;
use App\Models\Departement;
use App\Services\MailService;
use Carbon\Carbon;

class AdministrationController extends Controller
{
    protected $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    /**
     * Afficher la page d'administration
     */
    public function index()
    {
        $users = User::with(['role', 'departement'])
            ->where('id_user', '!=', auth()->id()) // Exclure l'admin connecté
            ->orderBy('created_at', 'desc')
            ->get();

        $roles = Role::all();
        $departements = Departement::all();

        return view('admin.administration', compact('users', 'roles', 'departements'));
    }

    /**
     * Récupérer tous les utilisateurs (API)
     */
    public function getUsers()
    {
        try {
            $users = User::with(['role', 'departement'])
                ->where('id_user', '!=', auth()->id())
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'users' => $users
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur récupération utilisateurs', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des utilisateurs'
            ], 500);
        }
    }

    /**
     * Créer un nouvel utilisateur et envoyer email d'activation
     */
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'telephone' => 'nullable|string|max:20',
            'profession' => 'nullable|string|max:100',
            'matricule' => 'required|string|max:20|unique:users,matricule',
            'date_embauche' => 'required|date',
            'role_id' => 'required|exists:roles,id_role',
            'departement_id' => 'nullable|exists:departements,id_departement',
            'solde_conges_annuel' => 'required|integer|min:0|max:60',
        ], [
            'nom.required' => 'Le nom est obligatoire',
            'prenom.required' => 'Le prénom est obligatoire',
            'email.required' => 'L\'email est obligatoire',
            'email.unique' => 'Cet email est déjà utilisé',
            'matricule.required' => 'Le matricule est obligatoire',
            'matricule.unique' => 'Ce matricule est déjà utilisé',
            'date_embauche.required' => 'La date d\'embauche est obligatoire',
            'role_id.required' => 'Le rôle est obligatoire',
            'role_id.exists' => 'Le rôle sélectionné n\'existe pas',
            'departement_id.exists' => 'Le département sélectionné n\'existe pas',
            'solde_conges_annuel.required' => 'Le solde de congés est obligatoire',
        ]);

        try {
            DB::beginTransaction();

            // Créer l'utilisateur avec un mot de passe temporaire
            $temporaryPassword = Str::random(32);

            $user = User::create([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'password' => Hash::make($temporaryPassword),
                'telephone' => $request->telephone,
                'profession' => $request->profession,
                'matricule' => $request->matricule,
                'date_embauche' => $request->date_embauche,
                'role_id' => $request->role_id,
                'departement_id' => $request->departement_id,
                'solde_conges_annuel' => $request->solde_conges_annuel,
                'conges_pris' => 0,
                'actif' => false, // Compte inactif par défaut jusqu'à activation
            ]);

            // Générer un token d'activation unique
            $token = Str::random(64);

            // Supprimer les anciens tokens pour cet email
            DB::table('account_activations')->where('email', $request->email)->delete();

            // Sauvegarder le nouveau token
            DB::table('account_activations')->insert([
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => now()
            ]);

            // Envoyer l'email d'activation
            try {
                $emailSent = $this->mailService->envoyerActivationCompte(
                    $user->email,
                    $user->nom,
                    $user->prenom,
                    $token
                );

                if (!$emailSent) {
                    Log::warning('Échec envoi email activation', [
                        'user_id' => $user->id_user,
                        'email' => $user->email
                    ]);
                }
            } catch (\Exception $mailException) {
                Log::error('Erreur envoi email activation', [
                    'error' => $mailException->getMessage(),
                    'user_id' => $user->id_user,
                    'email' => $user->email
                ]);
            }

            DB::commit();

            Log::info('Utilisateur créé avec succès', [
                'user_id' => $user->id_user,
                'email' => $user->email,
                'created_by' => auth()->id()
            ]);

            // Charger les relations pour la réponse
            $user->load(['role', 'departement']);

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur créé avec succès. Un email d\'activation a été envoyé.',
                'user' => $user
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Erreur création utilisateur', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'utilisateur: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher les détails d'un utilisateur
     */
    public function show($id)
    {
        try {
            $user = User::with(['role', 'departement'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'user' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non trouvé'
            ], 404);
        }
    }

    /**
     * Mettre à jour un utilisateur
     */
    public function update(Request $request, $id)
    {
        // Validation
        $request->validate([
            'nom' => 'required|string|max:100',
            'prenom' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $id . ',id_user',
            'telephone' => 'nullable|string|max:20',
            'profession' => 'nullable|string|max:100',
            'matricule' => 'required|string|max:20|unique:users,matricule,' . $id . ',id_user',
            'date_embauche' => 'required|date',
            'role_id' => 'required|exists:roles,id_role',
            'departement_id' => 'nullable|exists:departements,id_departement',
            'solde_conges_annuel' => 'required|integer|min:0|max:60',
        ]);

        try {
            $user = User::findOrFail($id);

            $user->update([
                'nom' => $request->nom,
                'prenom' => $request->prenom,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'profession' => $request->profession,
                'matricule' => $request->matricule,
                'date_embauche' => $request->date_embauche,
                'role_id' => $request->role_id,
                'departement_id' => $request->departement_id,
                'solde_conges_annuel' => $request->solde_conges_annuel,
            ]);

            Log::info('Utilisateur mis à jour', [
                'user_id' => $user->id_user,
                'updated_by' => auth()->id()
            ]);

            // Charger les relations pour la réponse
            $user->load(['role', 'departement']);

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur mis à jour avec succès',
                'user' => $user
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur mise à jour utilisateur', [
                'error' => $e->getMessage(),
                'user_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de l\'utilisateur'
            ], 500);
        }
    }

    /**
     * Bloquer un utilisateur
     */
    public function block($id)
    {
        try {
            $user = User::findOrFail($id);

            // Ne pas bloquer un admin
            if ($user->role && $user->role->nom_role === 'Admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de bloquer un administrateur'
                ], 403);
            }

            $user->update(['actif' => false]);

            Log::info('Utilisateur bloqué', [
                'user_id' => $user->id_user,
                'blocked_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur bloqué avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur blocage utilisateur', [
                'error' => $e->getMessage(),
                'user_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du blocage de l\'utilisateur'
            ], 500);
        }
    }

    /**
     * Débloquer un utilisateur
     */
    public function unblock($id)
    {
        try {
            $user = User::findOrFail($id);

            $user->update(['actif' => true]);

            Log::info('Utilisateur débloqué', [
                'user_id' => $user->id_user,
                'unblocked_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur débloqué avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur déblocage utilisateur', [
                'error' => $e->getMessage(),
                'user_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du déblocage de l\'utilisateur'
            ], 500);
        }
    }

    /**
     * Supprimer un utilisateur
     */
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            // Ne pas supprimer un admin
            if ($user->role && $user->role->nom_role === 'Admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer un administrateur'
                ], 403);
            }

            // Supprimer le token d'activation s'il existe
            DB::table('account_activations')->where('email', $user->email)->delete();

            $userEmail = $user->email;
            $user->delete();

            Log::info('Utilisateur supprimé', [
                'user_email' => $userEmail,
                'deleted_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur suppression utilisateur', [
                'error' => $e->getMessage(),
                'user_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression de l\'utilisateur'
            ], 500);
        }
    }

    /**
     * Renvoyer l'email d'activation
     */
    public function resendActivation($id)
    {
        try {
            $user = User::findOrFail($id);

            // Vérifier si le compte est déjà activé
            if ($user->actif) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce compte est déjà activé'
                ], 400);
            }

            // Générer un nouveau token
            $token = Str::random(64);

            // Supprimer les anciens tokens
            DB::table('account_activations')->where('email', $user->email)->delete();

            // Créer le nouveau token
            DB::table('account_activations')->insert([
                'email' => $user->email,
                'token' => Hash::make($token),
                'created_at' => now()
            ]);

            // Envoyer l'email
            $emailSent = $this->mailService->envoyerActivationCompte(
                $user->email,
                $user->nom,
                $user->prenom,
                $token
            );

            if ($emailSent) {
                Log::info('Email d\'activation renvoyé', [
                    'user_id' => $user->id_user,
                    'email' => $user->email
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Email d\'activation renvoyé avec succès'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de l\'envoi de l\'email'
                ], 500);
            }

        } catch (\Exception $e) {
            Log::error('Erreur renvoi activation', [
                'error' => $e->getMessage(),
                'user_id' => $id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du renvoi de l\'email d\'activation'
            ], 500);
        }
    }

    /**
     * Générer un nouveau matricule automatiquement
     */
    public function generateMatricule(Request $request)
    {
        try {
            $roleId = $request->role_id;
            $role = Role::find($roleId);

            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Rôle non trouvé'
                ], 404);
            }

            // Préfixe selon le rôle
            $prefix = 'EMP'; // Par défaut

            if ($role->nom_role === 'Admin') {
                $prefix = 'ADM';
            } elseif ($role->nom_role === 'Manager' || $role->nom_role === 'chef de departement') {
                $prefix = 'MGR';
            }

            // Trouver le dernier matricule avec ce préfixe
            $lastUser = User::where('matricule', 'LIKE', $prefix . '%')
                ->orderBy('matricule', 'desc')
                ->first();

            if ($lastUser) {
                // Extraire le numéro et incrémenter
                $lastNumber = intval(substr($lastUser->matricule, 3));
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }

            $matricule = $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);

            return response()->json([
                'success' => true,
                'matricule' => $matricule
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur génération matricule', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du matricule'
            ], 500);
        }
    }
}
