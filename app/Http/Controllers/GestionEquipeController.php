<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Departement;

class GestionEquipeController extends Controller
{
    /**
     * Afficher la page de gestion d'équipe
     */
    public function index()
    {
        $user = Auth::user();

        // Vérifier que l'utilisateur est chef de département
        if ($user->role_id !== 3) {
            return redirect()->back()->with('error', 'Accès non autorisé');
        }

        // Récupérer le département du chef
        $departement = Departement::find($user->departement_id);

        return view('chef-de-departement.gestion-equipe', compact('departement'));
    }

    /**
     * Générer les initiales à partir du prénom et nom
     */
    private function generateInitials($prenom, $nom)
    {
        $prenom = $prenom ?? '';
        $nom = $nom ?? '';
        return strtoupper(substr($prenom, 0, 1) . substr($nom, 0, 1));
    }

    /**
     * Générer la photo de profil (URL ou initiales)
     */
    private function generatePhotoUrl($employee)
    {
        if (!empty($employee->photo_url)) {
            return [
                'type' => 'url',
                'value' => $employee->photo_url
            ];
        }

        $initiales = $this->generateInitials($employee->prenom, $employee->nom);
        $nomComplet = trim(($employee->prenom ?? '') . ' ' . ($employee->nom ?? ''));

        // Couleurs de gradient cohérentes
        $colors = [
            'from-purple-400 to-pink-400',
            'from-blue-400 to-indigo-400',
            'from-green-400 to-teal-400',
            'from-orange-400 to-red-400',
            'from-yellow-400 to-orange-400',
            'from-pink-400 to-rose-400',
        ];
        $colorIndex = strlen($nomComplet) % count($colors);
        $gradient = $colors[$colorIndex];

        return [
            'type' => 'initials',
            'initials' => $initiales,
            'gradient' => $gradient
        ];
    }

    /**
     * Récupérer tous les employés du département
     */
    public function getEmployees(Request $request)
    {
        try {
            $user = Auth::user();

            // Vérifier que l'utilisateur est chef de département
            if ($user->role_id !== 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            // Récupérer les paramètres de recherche et filtrage
            $search = $request->input('search', '');
            $position = $request->input('position', '');
            $page = $request->input('page', 1);
            $perPage = 5;

            // Query de base : tous les employés du département (sauf le chef lui-même)
            $query = User::where('departement_id', $user->departement_id)
                        ->where('id_user', '!=', $user->id_user)
                        ->with('role');

            // Filtre de recherche
            if (!empty($search)) {
                $query->where(function($q) use ($search) {
                    $q->where('nom', 'LIKE', "%{$search}%")
                      ->orWhere('prenom', 'LIKE', "%{$search}%")
                      ->orWhere('email', 'LIKE', "%{$search}%");
                });
            }

            // Filtre par poste
            if (!empty($position)) {
                $query->where('profession', $position);
            }

            // Pagination
            $total = $query->count();
            $employees = $query->orderBy('nom')
                              ->skip(($page - 1) * $perPage)
                              ->take($perPage)
                              ->get();

            // Formater les données
            $formattedEmployees = $employees->map(function($employee) {
                $photoData = $this->generatePhotoUrl($employee);

                return [
                    'id' => $employee->id_user,
                    'nom' => $employee->nom,
                    'prenom' => $employee->prenom,
                    'name' => $employee->prenom . ' ' . $employee->nom,
                    'email' => $employee->email,
                    'phone' => $employee->telephone ?? 'N/A',
                    'position' => $employee->profession,
                    'positionLabel' => $employee->profession,
                    'photo' => $photoData,
                    'blocked' => !$employee->actif,
                    'matricule' => $employee->matricule,
                    'date_embauche' => $employee->date_embauche,
                    'solde_conges' => $employee->solde_conges_annuel,
                    'conges_pris' => $employee->conges_pris
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $formattedEmployees,
                'pagination' => [
                    'total' => $total,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'total_pages' => ceil($total / $perPage)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des employés: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les détails d'un employé
     */
    public function getEmployeeDetails($id)
    {
        try {
            $user = Auth::user();

            // Vérifier que l'utilisateur est chef de département
            if ($user->role_id !== 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            // Récupérer l'employé
            $employee = User::where('id_user', $id)
                          ->where('departement_id', $user->departement_id)
                          ->with(['role', 'departement', 'demandesConges.typeConge'])
                          ->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employé non trouvé'
                ], 404);
            }

            // Formater les congés
            $conges = $employee->demandesConges->map(function($demande) {
                return [
                    'id' => $demande->id_demande,
                    'type' => $demande->typeConge->nom_type,
                    'date_debut' => $demande->date_debut,
                    'date_fin' => $demande->date_fin,
                    'nb_jours' => $demande->nb_jours,
                    'statut' => $demande->statut,
                    'motif' => $demande->motif
                ];
            });

            $photoData = $this->generatePhotoUrl($employee);

            $data = [
                'id' => $employee->id_user,
                'nom' => $employee->nom,
                'prenom' => $employee->prenom,
                'name' => $employee->prenom . ' ' . $employee->nom,
                'email' => $employee->email,
                'phone' => $employee->telephone ?? 'N/A',
                'position' => $employee->profession,
                'positionLabel' => $employee->profession,
                'photo' => $photoData,
                'blocked' => !$employee->actif,
                'matricule' => $employee->matricule,
                'date_embauche' => $employee->date_embauche,
                'solde_conges' => $employee->solde_conges_annuel,
                'conges_pris' => $employee->conges_pris,
                'departement' => $employee->departement->nom_departement ?? 'N/A',
                'role' => $employee->role->nom_role ?? 'N/A',
                'conges' => $conges
            ];

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des détails: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bloquer ou débloquer un employé
     */
    public function toggleBlockEmployee(Request $request, $id)
    {
        try {
            $user = Auth::user();

            // Vérifier que l'utilisateur est chef de département
            if ($user->role_id !== 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            // Récupérer l'employé
            $employee = User::where('id_user', $id)
                          ->where('departement_id', $user->departement_id)
                          ->first();

            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employé non trouvé'
                ], 404);
            }

            // Empêcher de se bloquer soi-même
            if ($employee->id_user === $user->id_user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous ne pouvez pas modifier votre propre statut'
                ], 400);
            }

            // Inverser le statut
            $employee->actif = !$employee->actif;
            $employee->save();

            $status = $employee->actif ? 'débloqué' : 'bloqué';

            return response()->json([
                'success' => true,
                'message' => "{$employee->prenom} {$employee->nom} a été {$status} avec succès",
                'data' => [
                    'id' => $employee->id_user,
                    'actif' => $employee->actif,
                    'blocked' => !$employee->actif
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification du statut: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les postes uniques du département
     */
    public function getPositions()
    {
        try {
            $user = Auth::user();

            if ($user->role_id !== 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            $positions = User::where('departement_id', $user->departement_id)
                            ->whereNotNull('profession')
                            ->distinct()
                            ->pluck('profession');

            return response()->json([
                'success' => true,
                'data' => $positions
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des postes'
            ], 500);
        }
    }
}
