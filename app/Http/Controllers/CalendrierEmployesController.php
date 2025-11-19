<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\User;
use App\Models\TypeConge;
use App\Models\DemandeConge;

class CalendrierEmployesController extends Controller
{
    /**
     * Affiche la page du calendrier de l'employé
     */
    public function index()
    {
        return view('employes.calendrier-employes');
    }

    /**
     * Récupère toutes les données de congés de l'employé connecté
     */
    public function getEmployeeLeaveData()
    {
        try {
            $user = Auth::user();

            // Récupérer les informations de base de l'employé
            $employeeInfo = [
                'id' => $user->id_user,
                'nom' => $user->nom,
                'prenom' => $user->prenom,
                'matricule' => $user->matricule,
                'date_embauche' => $user->date_embauche,
                'solde_conges_annuel' => $user->solde_conges_annuel,
                'conges_pris' => $user->conges_pris,
            ];

            // Récupérer tous les types de congés actifs avec leurs couleurs
            $typesConges = TypeConge::where('actif', 1)
                ->select('id_type', 'nom_type', 'couleur_calendrier', 'duree_max_jours', 'necessite_justificatif')
                ->get()
                ->map(function ($type) {
                    return [
                        'id' => $type->id_type,
                        'nom' => $type->nom_type,
                        'couleur' => $type->couleur_calendrier,
                        'duree_max' => $type->duree_max_jours,
                        'necessite_justificatif' => (bool) $type->necessite_justificatif
                    ];
                });

            // Récupérer toutes les demandes de congés approuvées de l'employé avec leurs relations
            $congesApprouves = DemandeConge::with('typeConge')
                ->where('user_id', $user->id_user)
                ->where('statut', 'Approuvé')
                ->orderBy('date_debut', 'asc')
                ->get()
                ->map(function ($conge) {
                    return [
                        'id' => $conge->id_demande,
                        'type_id' => $conge->type_conge_id,
                        'type_nom' => $conge->typeConge->nom_type,
                        'couleur' => $conge->typeConge->couleur_calendrier,
                        'date_debut' => $conge->date_debut,
                        'date_fin' => $conge->date_fin,
                        'nb_jours' => $conge->nb_jours,
                        'motif' => $conge->motif,
                        'date_validation' => $conge->date_validation,
                        'jours_ecoules' => $this->calculerJoursEcoules($conge->date_debut, $conge->date_fin),
                        'pourcentage' => $this->calculerPourcentage($conge->date_debut, $conge->date_fin, $conge->nb_jours)
                    ];
                });

            // Calculer les statistiques par type de congé
            $statistiquesParType = [];
            foreach ($typesConges as $type) {
                $congesDuType = $congesApprouves->where('type_id', $type['id']);

                $totalJours = $congesDuType->sum('nb_jours');
                $joursEcoules = $congesDuType->sum('jours_ecoules');
                $joursRestants = $totalJours - $joursEcoules;

                $statistiquesParType[] = [
                    'type_id' => $type['id'],
                    'type_nom' => $type['nom'],
                    'couleur' => $type['couleur'],
                    'total_jours' => $totalJours,
                    'jours_ecoules' => $joursEcoules,
                    'jours_restants' => max(0, $joursRestants),
                    'pourcentage' => $totalJours > 0 ? round(($joursEcoules / $totalJours) * 100, 1) : 0,
                    'duree_max' => $type['duree_max']
                ];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'employe' => $employeeInfo,
                    'types_conges' => $typesConges,
                    'conges' => $congesApprouves,
                    'statistiques' => $statistiquesParType
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des données: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Calcule le nombre de jours écoulés depuis le début du congé (en excluant les weekends)
     */
    private function calculerJoursEcoules($dateDebut, $dateFin)
    {
        $debut = Carbon::parse($dateDebut);
        $fin = Carbon::parse($dateFin);
        $aujourd_hui = Carbon::now();

        // Si le congé n'a pas encore commencé
        if ($debut->isFuture()) {
            return 0;
        }

        // Si le congé est terminé, retourner tous les jours ouvrables
        $dateReference = $aujourd_hui->isAfter($fin) ? $fin : $aujourd_hui;

        $joursEcoules = 0;
        $current = $debut->copy();

        while ($current->lte($dateReference)) {
            // Exclure les weekends (samedi = 6, dimanche = 0)
            if (!$current->isWeekend()) {
                // Note: Les jours fériés sont gérés côté JavaScript
                // mais vous pouvez ajouter une vérification ici si nécessaire
                $joursEcoules++;
            }
            $current->addDay();
        }

        return $joursEcoules;
    }

    /**
     * Calcule le pourcentage d'avancement du congé
     */
    private function calculerPourcentage($dateDebut, $dateFin, $nbJoursTotal)
    {
        $joursEcoules = $this->calculerJoursEcoules($dateDebut, $dateFin);

        if ($nbJoursTotal <= 0) {
            return 0;
        }

        $pourcentage = ($joursEcoules / $nbJoursTotal) * 100;
        return round(min(100, max(0, $pourcentage)), 1);
    }

    /**
     * Récupère les détails d'un congé spécifique
     */
    public function getLeaveDetails($id)
    {
        try {
            $user = Auth::user();

            $conge = DemandeConge::with(['typeConge', 'validateur'])
                ->where('id_demande', $id)
                ->where('user_id', $user->id_user)
                ->first();

            if (!$conge) {
                return response()->json([
                    'success' => false,
                    'message' => 'Congé non trouvé'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $conge->id_demande,
                    'type_nom' => $conge->typeConge->nom_type,
                    'couleur' => $conge->typeConge->couleur_calendrier,
                    'date_debut' => $conge->date_debut,
                    'date_fin' => $conge->date_fin,
                    'nb_jours' => $conge->nb_jours,
                    'motif' => $conge->motif,
                    'statut' => $conge->statut,
                    'date_validation' => $conge->date_validation,
                    'validateur' => $conge->validateur ? $conge->validateur->nom . ' ' . $conge->validateur->prenom : null,
                    'document_justificatif' => $conge->document_justificatif,
                    'jours_ecoules' => $this->calculerJoursEcoules($conge->date_debut, $conge->date_fin),
                    'pourcentage' => $this->calculerPourcentage($conge->date_debut, $conge->date_fin, $conge->nb_jours)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des détails: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère les congés pour un mois spécifique
     */
    public function getLeavesByMonth(Request $request)
    {
        try {
            $user = Auth::user();
            $mois = $request->input('mois', date('m'));
            $annee = $request->input('annee', date('Y'));

            $conges = DemandeConge::with('typeConge')
                ->where('user_id', $user->id_user)
                ->where('statut', 'Approuvé')
                ->whereYear('date_debut', '<=', $annee)
                ->whereYear('date_fin', '>=', $annee)
                ->where(function($query) use ($mois, $annee) {
                    $query->where(function($q) use ($mois, $annee) {
                        $q->whereMonth('date_debut', '<=', $mois)
                          ->whereYear('date_debut', $annee);
                    })
                    ->orWhere(function($q) use ($mois, $annee) {
                        $q->whereMonth('date_fin', '>=', $mois)
                          ->whereYear('date_fin', $annee);
                    });
                })
                ->get();

            return response()->json([
                'success' => true,
                'data' => $conges
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur: ' . $e->getMessage()
            ], 500);
        }
    }
}
