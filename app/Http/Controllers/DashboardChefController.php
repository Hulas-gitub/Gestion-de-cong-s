<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Departement;
use App\Models\DemandeConge;
use App\Models\TypeConge;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardChefController extends Controller
{
    /**
     * Affiche le dashboard du chef de département
     */
    public function index()
    {
        return view('chef-de-departement.tableau-de-bord-manager');
    }

    /**
     * Récupère les statistiques KPIs du chef de département
     */
    public function getKpiStats()
    {
        try {
            $chefId = Auth::id();

            // Récupérer le département dont l'utilisateur est chef
            $departement = Departement::where('chef_departement_id', $chefId)
                ->where('actif', 1)
                ->first();

            if (!$departement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes chef d\'aucun département'
                ], 403);
            }

            // 1. Nombre total de demandes en attente dans son département
            $demandesEnAttente = DemandeConge::whereHas('user', function($query) use ($departement) {
                $query->where('departement_id', $departement->id_departement)
                      ->where('actif', 1);
            })
            ->where('statut', 'En attente')
            ->count();

            // 2. Nombre total d'employés du département avec nom du département
            $totalEmployes = User::where('departement_id', $departement->id_departement)
                ->where('actif', 1)
                ->where('id_user', '!=', $chefId) // Exclure le chef lui-même
                ->count();

            // 3. Nombre total de demandes validées dans le mois en cours
            $anneeActuelle = Carbon::now()->year;
            $moisActuel = Carbon::now()->month;

            $demandesValideesMois = DemandeConge::whereHas('user', function($query) use ($departement) {
                $query->where('departement_id', $departement->id_departement)
                      ->where('actif', 1);
            })
            ->where('statut', 'Approuvé')
            ->whereYear('created_at', $anneeActuelle)
            ->whereMonth('created_at', $moisActuel)
            ->count();

            // 4. Nombre total de demandes refusées (toutes périodes)
            $demandesRefusees = DemandeConge::whereHas('user', function($query) use ($departement) {
                $query->where('departement_id', $departement->id_departement)
                      ->where('actif', 1);
            })
            ->where('statut', 'Refusé')
            ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'nom_departement' => $departement->nom_departement,
                    'demandes_en_attente' => $demandesEnAttente,
                    'total_employes' => $totalEmployes,
                    'demandes_validees_mois' => $demandesValideesMois,
                    'demandes_refusees' => $demandesRefusees
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des KPIs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Évolution des demandes par employé sur 12 mois (à partir de novembre année en cours)
     * Compte uniquement les demandes APPROUVÉES
     */
    public function getEvolutionDemandesParEmploye()
    {
        try {
            $chefId = Auth::id();

            // Récupérer le département du chef
            $departement = Departement::where('chef_departement_id', $chefId)
                ->where('actif', 1)
                ->first();

            if (!$departement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Département non trouvé'
                ], 403);
            }

            $anneeActuelle = Carbon::now()->year;
            $moisDebut = 11; // Novembre

            // Récupérer tous les employés du département
            $employes = User::where('departement_id', $departement->id_departement)
                ->where('actif', 1)
                ->where('id_user', '!=', $chefId)
                ->get();

            $evolutionData = [];

            // Préparer les données pour chaque employé
            foreach ($employes as $employe) {
                $employeData = [
                    'nom_complet' => $employe->prenom . ' ' . $employe->nom,
                    'matricule' => $employe->matricule,
                    'demandes_par_mois' => []
                ];

                // Pour chaque mois (12 mois à partir de novembre)
                for ($i = 0; $i < 12; $i++) {
                    $mois = ($moisDebut + $i - 1) % 12 + 1;
                    $annee = $anneeActuelle;

                    // Si on dépasse décembre, on passe à l'année suivante
                    if (($moisDebut + $i) > 12) {
                        $annee = $anneeActuelle + 1;
                    }

                    // Compter les demandes APPROUVÉES de cet employé pour ce mois
                    $nombreDemandes = DemandeConge::where('user_id', $employe->id_user)
                        ->where('statut', 'Approuvé')
                        ->whereYear('date_debut', $annee)
                        ->whereMonth('date_debut', $mois)
                        ->count();

                    $nomMois = Carbon::create($annee, $mois, 1)->locale('fr')->isoFormat('MMMM');

                    $employeData['demandes_par_mois'][] = [
                        'mois' => ucfirst($nomMois),
                        'annee' => $annee,
                        'nombre' => $nombreDemandes
                    ];
                }

                $evolutionData[] = $employeData;
            }

            return response()->json([
                'success' => true,
                'data' => $evolutionData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'évolution des demandes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Répartition par type de congé (avec couleurs du calendrier)
     * Pour les employés du département du chef
     */
    public function getTypesConges()
    {
        try {
            $chefId = Auth::id();

            // Récupérer le département du chef
            $departement = Departement::where('chef_departement_id', $chefId)
                ->where('actif', 1)
                ->first();

            if (!$departement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Département non trouvé'
                ], 403);
            }

            // Récupérer les IDs des employés du département
            $employesIds = User::where('departement_id', $departement->id_departement)
                ->where('actif', 1)
                ->pluck('id_user');

            // Récupérer tous les types de congés avec le nombre de demandes
            $typesConges = TypeConge::where('actif', 1)
                ->withCount(['demandes' => function($query) use ($employesIds) {
                    $query->whereIn('user_id', $employesIds);
                }])
                ->get()
                ->map(function($type) {
                    return [
                        'nom' => $type->nom_type,
                        'nombre' => $type->demandes_count,
                        'couleur' => $type->couleur_calendrier
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $typesConges
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des types de congés',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Taux d'approbation sur 12 mois (année en cours)
     * Calcule le pourcentage des demandes approuvées
     */
    public function getTauxApprobation()
    {
        try {
            $chefId = Auth::id();

            // Récupérer le département du chef
            $departement = Departement::where('chef_departement_id', $chefId)
                ->where('actif', 1)
                ->first();

            if (!$departement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Département non trouvé'
                ], 403);
            }

            $anneeActuelle = Carbon::now()->year;
            $moisDebut = 11; // Novembre

            // Récupérer les IDs des employés du département
            $employesIds = User::where('departement_id', $departement->id_departement)
                ->where('actif', 1)
                ->pluck('id_user');

            $tauxData = [];

            // Pour chaque mois (12 mois à partir de novembre)
            for ($i = 0; $i < 12; $i++) {
                $mois = ($moisDebut + $i - 1) % 12 + 1;
                $annee = $anneeActuelle;

                // Si on dépasse décembre, on passe à l'année suivante
                if (($moisDebut + $i) > 12) {
                    $annee = $anneeActuelle + 1;
                }

                // Total des demandes du mois
                $totalDemandes = DemandeConge::whereIn('user_id', $employesIds)
                    ->whereYear('created_at', $annee)
                    ->whereMonth('created_at', $mois)
                    ->count();

                // Demandes approuvées du mois
                $demandesApprouvees = DemandeConge::whereIn('user_id', $employesIds)
                    ->where('statut', 'Approuvé')
                    ->whereYear('created_at', $annee)
                    ->whereMonth('created_at', $mois)
                    ->count();

                // Calcul du pourcentage
                $tauxApprobation = $totalDemandes > 0
                    ? round(($demandesApprouvees / $totalDemandes) * 100, 1)
                    : 0;

                $nomMois = Carbon::create($annee, $mois, 1)->locale('fr')->isoFormat('MMMM');

                $tauxData[] = [
                    'mois' => ucfirst($nomMois),
                    'annee' => $annee,
                    'taux' => $tauxApprobation,
                    'total' => $totalDemandes,
                    'approuvees' => $demandesApprouvees
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $tauxData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du calcul du taux d\'approbation',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
