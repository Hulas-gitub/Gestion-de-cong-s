<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Departement;
use App\Models\DemandeConge;
use App\Models\TypeConge;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardAdminController extends Controller
{
    /**
     * Affiche le dashboard admin
     */
    public function index()
    {
        return view('admin.dashboard-admin');
    }

    /**
     * Récupère les statistiques KPIs
     */
    public function getKpiStats()
    {
        try {
            // 1. Nombre total d'employés actifs
            $totalEmployesActifs = User::where('actif', 1)->count();

            // 2. Nombre total de départements
            $totalDepartements = Departement::where('actif', 1)->count();

            // 3. Nombre total de chefs de département (role_id = 3)
            $totalChefs = User::where('role_id', 3)
                ->where('actif', 1)
                ->count();

            // 4. Nombre total d'employés en congé
            // (statut Approuvé + date actuelle entre date_debut et date_fin)
            $dateActuelle = Carbon::now()->format('Y-m-d');
            $totalEnConge = DemandeConge::where('statut', 'Approuvé')
                ->whereDate('date_debut', '<=', $dateActuelle)
                ->whereDate('date_fin', '>=', $dateActuelle)
                ->distinct('user_id')
                ->count('user_id');

            // 5. Nombre total de demandes en attente
            $totalEnAttente = DemandeConge::where('statut', 'En attente')->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'employes_actifs' => $totalEmployesActifs,
                    'total_departements' => $totalDepartements,
                    'total_chefs' => $totalChefs,
                    'employes_en_conge' => $totalEnConge,
                    'demandes_en_attente' => $totalEnAttente
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
     * Évolution des congés approuvés sur 12 mois (à partir de novembre 2025)
     */
    public function getEvolutionConges()
    {
        try {
            $anneeActuelle = Carbon::now()->year;
            $moisActuel = Carbon::now()->month;

            $moisDebut = 11; // Novembre
            $evolutionData = [];

            for ($i = 0; $i < 12; $i++) {
                $mois = ($moisDebut + $i - 1) % 12 + 1;
                $annee = $anneeActuelle;

                // Si on dépasse décembre, on passe à l'année suivante
                if (($moisDebut + $i) > 12) {
                    $annee = $anneeActuelle + 1;
                }

                // Compte les congés approuvés par date de début
                $nombreConges = DemandeConge::where('statut', 'Approuvé')
                    ->whereYear('date_debut', $annee)
                    ->whereMonth('date_debut', $mois)
                    ->count();

                $nomMois = Carbon::create($annee, $mois, 1)->locale('fr')->isoFormat('MMMM');

                $evolutionData[] = [
                    'mois' => ucfirst($nomMois),
                    'annee' => $annee,
                    'nombre' => $nombreConges
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $evolutionData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'évolution',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Nombre d'employés par département
     */
    public function getEmployesParDepartement()
    {
        try {
            $departements = Departement::where('actif', 1)
                ->withCount(['employes' => function($query) {
                    $query->where('actif', 1);
                }])
                ->get()
                ->map(function($dept, $index) {
                    return [
                        'nom' => $dept->nom_departement,
                        'nombre' => $dept->employes_count,
                        'couleur' => $this->generateColor($index)
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $departements
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des employés par département',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Répartition par type de congé
     */
    public function getTypesConges()
    {
        try {
            $typesConges = TypeConge::where('actif', 1)
                ->withCount('demandes')
                ->get()
                ->map(function($type) {
                    return [
                        'nom' => $type->nom_type,
                        'nombre' => $type->demandes_count,
                        'couleur' => $this->getTypeCongeCouleur($type->nom_type)
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
     * Taux d'absentéisme par département
     */
    public function getTauxAbsenteisme()
    {
        try {
            $dateActuelle = Carbon::now()->format('Y-m-d');

            $departements = Departement::where('actif', 1)
                ->with(['employes' => function($query) {
                    $query->where('actif', 1);
                }])
                ->get()
                ->map(function($dept) use ($dateActuelle) {
                    $totalEmployes = $dept->employes->count();

                    if ($totalEmployes == 0) {
                        return [
                            'nom' => $dept->nom_departement,
                            'taux' => 0
                        ];
                    }

                    // Employés en congé avec statut approuvé
                    // et date actuelle entre date_debut et date_fin
                    $employesEnConge = DemandeConge::whereIn('user_id', $dept->employes->pluck('id_user'))
                        ->where('statut', 'Approuvé')
                        ->whereDate('date_debut', '<=', $dateActuelle)
                        ->whereDate('date_fin', '>=', $dateActuelle)
                        ->distinct('user_id')
                        ->count('user_id');

                    $taux = ($employesEnConge / $totalEmployes) * 100;

                    return [
                        'nom' => $dept->nom_departement,
                        'taux' => round($taux, 1)
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $departements
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du calcul du taux d\'absentéisme',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Vue d'ensemble par département pour le tableau
     * Filtrage par période : mois, trimestre, année
     */
    public function getVueEnsemble(Request $request)
    {
        try {
            $periode = $request->get('periode', 'mois');
            $dateActuelle = Carbon::now();
            $dateActuelleStr = $dateActuelle->format('Y-m-d');

            // Déterminer la plage de dates selon la période DE L'ANNÉE EN COURS
            switch ($periode) {
                case 'trimestre':
                    // 3 derniers mois de l'année en cours
                    $dateDebut = $dateActuelle->copy()->subMonths(3)->startOfMonth();
                    $dateFin = $dateActuelle->copy()->endOfMonth();
                    break;
                case 'annee':
                    // Toute l'année en cours (du 1er janvier à aujourd'hui)
                    $dateDebut = $dateActuelle->copy()->startOfYear();
                    $dateFin = $dateActuelle->copy()->endOfDay();
                    break;
                default: // mois
                    // Mois en cours (du 1er du mois à aujourd'hui)
                    $dateDebut = $dateActuelle->copy()->startOfMonth();
                    $dateFin = $dateActuelle->copy()->endOfDay();
                    break;
            }

            $departements = Departement::where('actif', 1)
                ->with(['chefDepartement', 'employes' => function($query) {
                    $query->where('actif', 1);
                }])
                ->get()
                ->map(function($dept) use ($dateDebut, $dateFin, $dateActuelleStr) {
                    $employes = $dept->employes;
                    $totalEmployes = $employes->count();

                    // CORRECTION: Employés actuellement en congé
                    // (statut Approuvé + date actuelle entre date_debut et date_fin)
                    $employesEnConge = DemandeConge::whereIn('user_id', $employes->pluck('id_user'))
                        ->where('statut', 'Approuvé')
                        ->whereDate('date_debut', '<=', $dateActuelleStr)
                        ->whereDate('date_fin', '>=', $dateActuelleStr)
                        ->distinct('user_id')
                        ->count('user_id');

                    // Nombre de demandes sur la période SÉLECTIONNÉE
                    $demandes = DemandeConge::whereIn('user_id', $employes->pluck('id_user'))
                        ->whereBetween('created_at', [$dateDebut, $dateFin])
                        ->count();

                    // Calcul du solde moyen selon dates début/fin de la période
                    $soldeMoyen = $this->calculerSoldeMoyen($employes, $dateDebut, $dateFin);

                    // Taux d'absence
                    $tauxAbsence = $totalEmployes > 0 ? ($employesEnConge / $totalEmployes) * 100 : 0;

                    return [
                        'departement' => $dept->nom_departement,
                        'chef' => $dept->chefDepartement
                            ? $dept->chefDepartement->prenom . ' ' . $dept->chefDepartement->nom
                            : 'Non assigné',
                        'total_employes' => $totalEmployes,
                        'en_conge' => $employesEnConge,
                        'demandes' => $demandes,
                        'solde_moyen' => $soldeMoyen,
                        'taux_absence' => round($tauxAbsence, 1)
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $departements,
                'periode' => $periode
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de la vue d\'ensemble',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcule le solde moyen de congés des employés sur une période
     * Prend en compte les congés pris (nb_jours) selon dates début/fin
     */
    private function calculerSoldeMoyen($employes, $dateDebut, $dateFin)
    {
        if ($employes->isEmpty()) {
            return 0;
        }

        $totalSolde = 0;
        $nombreEmployes = $employes->count();

        foreach ($employes as $employe) {
            // Solde de base
            $soldeBase = $employe->solde_conges_annuel;

            // Congés pris sur la période (statut Approuvé)
            $congesPrisSurPeriode = DemandeConge::where('user_id', $employe->id_user)
                ->where('statut', 'Approuvé')
                ->where(function($query) use ($dateDebut, $dateFin) {
                    $query->whereBetween('date_debut', [$dateDebut, $dateFin])
                          ->orWhereBetween('date_fin', [$dateDebut, $dateFin])
                          ->orWhere(function($q) use ($dateDebut, $dateFin) {
                              $q->where('date_debut', '<=', $dateDebut)
                                ->where('date_fin', '>=', $dateFin);
                          });
                })
                ->sum('nb_jours');

            // Solde restant = solde de base - congés pris
            $soldeRestant = $soldeBase - $congesPrisSurPeriode;
            $totalSolde += $soldeRestant;
        }

        return round($totalSolde / $nombreEmployes, 1);
    }

    /**
     * Génère une couleur unique pour chaque département
     * S'adapte automatiquement même si > 10 départements
     */
    private function generateColor($index)
    {
        $colors = [
            'rgba(59, 130, 246, 0.8)',   // Bleu
            'rgba(168, 85, 247, 0.8)',   // Violet
            'rgba(236, 72, 153, 0.8)',   // Rose
            'rgba(34, 197, 94, 0.8)',    // Vert
            'rgba(249, 115, 22, 0.8)',   // Orange
            'rgba(239, 68, 68, 0.8)',    // Rouge
            'rgba(234, 179, 8, 0.8)',    // Jaune
            'rgba(20, 184, 166, 0.8)',   // Cyan
            'rgba(139, 92, 246, 0.8)',   // Indigo
            'rgba(244, 63, 94, 0.8)',    // Rouge rosé
        ];

        // Si index dépasse le tableau, générer une couleur HSL unique
        if ($index >= count($colors)) {
            $hue = ($index * 137.508) % 360; // Golden angle pour répartition optimale
            return "hsla($hue, 70%, 60%, 0.8)";
        }

        return $colors[$index];
    }

    /**
     * Retourne la couleur pour chaque type de congé
     */
    private function getTypeCongeCouleur($nomType)
    {
        $couleurs = [
            'Congés payés' => 'rgba(59, 130, 246, 0.8)',      // Bleu
            'Congé maladie' => 'rgba(239, 68, 68, 0.8)',      // Rouge
            'Congé autre' => 'rgba(168, 85, 247, 0.8)',       // Violet
            'Congé paternité' => 'rgba(20, 184, 166, 0.8)',   // Cyan
            'Congé maternité' => 'rgba(234, 179, 8, 0.8)',    // Jaune
        ];

        return $couleurs[$nomType] ?? 'rgba(156, 163, 175, 0.8)'; // Gris par défaut
    }
}
