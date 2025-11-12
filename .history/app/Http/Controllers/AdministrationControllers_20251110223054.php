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

class AdministrationControllers extends Controller
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
            ->where('id_user', '!=', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        $roles = Role::all();
        $departements = Departement::all();

        return view('admin.administration', compact('users', 'roles', 'departements'));
    }

    /**
     * Récupérer tous les utilisateurs avec pagination (API)
     */
    public function getUsers(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);
            $page = $request->input('page', 1);
            $roleFilter = $request->input('role'); // 'employe', 'chef', null

            $query = User::with(['role', 'departement'])
                ->where('id_user', '!=', auth()->id())
                ->orderBy('created_at', 'desc');

            // Filtrer par rôle si spécifié
            if ($roleFilter === 'employe') {
                $query->whereHas('role', function($q) {
                    $q->where('nom_role', 'emplpoyé'); // Attention à l'orthographe dans la BDD
                });
            } elseif ($roleFilter === 'chef') {
                $query->whereHas('role', function($q) {
                    $q->whereIn('nom_role', ['chef de departement', 'Chef de Département']);
                });
            }

            $users = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'users' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'from' => $users->firstItem(),
                    'to' => $users->lastItem()
                ]
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
     * Récupérer tous les départements (API)
     */
    public function getDepartements(Request $request)
    {
        try {
            $perPage = $request->input('per_page', 10);

            $departements = Departement::with(['chefDepartement'])
                ->withCount('employes')
                ->where('actif', true)
                ->orderBy('nom_departement')
                ->paginate($perPage);

            return response()->json([
                'success' => true,
                'departements' => $departements->items(),
                'pagination' => [
                    'current_page' => $departements->currentPage(),
                    'last_page' => $departements->lastPage(),
                    'per_page' => $departements->perPage(),
                    'total' => $departements->total()
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur récupération départements', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des départements'
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
                'actif' => false,
            ]);

            $token = Str::random(64);
            DB::table('account_activations')->where('email', $request->email)->delete();
            DB::table('account_activations')->insert([
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => now()
            ]);

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
                    'user_id' => $user->id_user
                ]);
            }

            DB::commit();

            Log::info('Utilisateur créé avec succès', [
                'user_id' => $user->id_user,
                'email' => $user->email,
                'created_by' => auth()->id()
            ]);

            $user->load(['role', 'departement']);

            return response()->json([
                'success' => true,
                'message' => 'Utilisateur créé avec succès. Un email d\'activation a été envoyé à ' . $user->email,
                'user' => $user
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur création utilisateur', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'utilisateur : ' . $e->getMessage()
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
                'message' => 'L\'utilisateur ' . $user->prenom . ' ' . $user->nom . ' a été bloqué avec succès'
            ]);

        } catch (\Exception $e) {
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
                'message' => 'L\'utilisateur ' . $user->prenom . ' ' . $user->nom . ' a été débloqué avec succès'
            ]);

        } catch (\Exception $e) {
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

            if ($user->role && $user->role->nom_role === 'Admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer un administrateur'
                ], 403);
            }

            $userName = $user->prenom . ' ' . $user->nom;
            DB::table('account_activations')->where('email', $user->email)->delete();
            $user->delete();

            Log::info('Utilisateur supprimé', [
                'user_name' => $userName,
                'deleted_by' => auth()->id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'L\'utilisateur ' . $userName . ' a été supprimé avec succès'
            ]);

        } catch (\Exception $e) {
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

            if ($user->actif) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ce compte est déjà activé'
                ], 400);
            }

            $token = Str::random(64);
            DB::table('account_activations')->where('email', $user->email)->delete();
            DB::table('account_activations')->insert([
                'email' => $user->email,
                'token' => Hash::make($token),
                'created_at' => now()
            ]);

            $emailSent = $this->mailService->envoyerActivationCompte(
                $user->email,
                $user->nom,
                $user->prenom,
                $token
            );

            if ($emailSent) {
                return response()->json([
                    'success' => true,
                    'message' => 'Email d\'activation renvoyé avec succès à ' . $user->email
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erreur lors de l\'envoi de l\'email d\'activation'
                ], 500);
            }

        } catch (\Exception $e) {
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

            $prefix = 'EMP';

            if ($role->nom_role === 'Admin') {
                $prefix = 'ADM';
            } elseif (in_array($role->nom_role, ['chef de departement', 'Chef de Département'])) {
                $prefix = 'MGR';
            }

            $lastUser = User::where('matricule', 'LIKE', $prefix . '%')
                ->orderBy('matricule', 'desc')
                ->first();

            if ($lastUser) {
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
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la génération du matricule'
            ], 500);
        }
    }

    /**
     * Créer un département
     */
    public function storeDepartement(Request $request)
    {
        $request->validate([
            'nom_departement' => 'required|string|max:100|unique:departements,nom_departement',
            'description' => 'nullable|string',
            'chef_departement_id' => 'nullable|exists:users,id_user',
            'couleur_calendrier' => 'nullable|string|max:7'
        ]);

        try {
            $departement = Departement::create([
                'nom_departement' => $request->nom_departement,
                'description' => $request->description,
                'chef_departement_id' => $request->chef_departement_id,
                'couleur_calendrier' => $request->couleur_calendrier ?? '#3b82f6',
                'actif' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Département créé avec succès',
                'departement' => $departement->load('chefDepartement')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du département'
            ], 500);
        }
    }

    /**
     * Mettre à jour un département
     */
    public function updateDepartement(Request $request, $id)
    {
        $request->validate([
            'nom_departement' => 'required|string|max:100|unique:departements,nom_departement,' . $id . ',id_departement',
            'description' => 'nullable|string',
            'chef_departement_id' => 'nullable|exists:users,id_user',
            'couleur_calendrier' => 'nullable|string|max:7'
        ]);

        try {
            $departement = Departement::findOrFail($id);

            $departement->update([
                'nom_departement' => $request->nom_departement,
                'description' => $request->description,
                'chef_departement_id' => $request->chef_departement_id,
                'couleur_calendrier' => $request->couleur_calendrier
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Département mis à jour avec succès',
                'departement' => $departement->load('chefDepartement')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du département'
            ], 500);
        }
    }

    /**
     * Supprimer un département
     */
    public function destroyDepartement($id)
    {
        try {
            $departement = Departement::findOrFail($id);

            // Vérifier s'il y a des employés dans ce département
            if ($departement->employes()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer un département contenant des employés'
                ], 400);
            }

            $departement->delete();

            return response()->json([
                'success' => true,
                'message' => 'Département supprimé avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du département'
            ], 500);
        }
    }
}
