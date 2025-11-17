<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DemandeConge;
use App\Models\TypeConge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CalendrierEmployesController extends Controller
{
    /**
     * Afficher la page du calendrier de l'employé
     */
    public function index()
    {
        $user = Auth::user();

        // Récupérer tous les types de congés avec leurs couleurs
        $typesConges = TypeConge::where('actif', 1)->get();

        return view('employes.calendrier-employers', compact('typesConges'));
    }

    /**
     * Récupérer les données du calendrier en AJAX
     */
    public function getData()
    {
        try {
            $user = Auth::user();

            // Calculer les informations de solde
            $balanceInfo = $this->calculerInfosSolde($user);

            // Récupérer toutes les demandes approuvées de l'employé
            $demandes = DemandeConge::where('user_id', $user->id_user)
                ->where('statut', 'Approuvé')
                ->with(['typeConge'])
                ->orderBy('date_debut', 'asc')
                ->get();

            // Formater les congés pour le calendrier
            $leaves = $demandes->map(function ($demande) {
                return [
                    'id' => $demande->id_demande,
                    'userId' => 'user-' . $demande->user_id,
                    'type' => $this->getLeaveTypeKey($demande->typeConge->nom_type),
                    'typeName' => $demande->typeConge->nom_type,
                    'typeColor' => $demande->typeConge->couleur ?? '#3b82f6',
                    'startDate' => $demande->date_debut,
                    'endDate' => $demande->date_fin,
                    'status' => 'approved',
                    'reason' => $demande->motif ?? 'Congé',
                    'nbJours' => $demande->nb_jours
                ];
            });

            // Récupérer tous les types de congés pour la légende
            $typesConges = TypeConge::where('actif', 1)->get()->map(function ($type) {
                return [
                    'id' => $type->id_type,
                    'nom' => $type->nom_type,
                    'couleur' => $type->couleur ?? '#3b82f6',
                    'key' => $this->getLeaveTypeKey($type->nom_type)
                ];
            });

            // Calculer la progression des congés consommés dans l'année
            $progressionAnnuelle = $this->calculerProgressionAnnuelle($user);

            return response()->json([
                'success' => true,
                'employeeConfig' => [
                    'name' => $user->nom . ' ' . $user->prenom,
                    'matricule' => $user->matricule,
                    'hireDate' => $user->created_at->format('Y-m-d'),
                    'userId' => 'user-' . $user->id_user
                ],
                'balance' => $balanceInfo,
                'leaves' => $leaves,
                'leaveTypes' => $typesConges,
                'yearProgress' => $progressionAnnuelle,
                'holidays' => $this->getJoursFeriesComplets()
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erreur récupération données calendrier: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des données du calendrier'
            ], 500);
        }
    }

    /**
     * Calculer les informations de solde de congés
     */
    private function calculerInfosSolde($user)
    {
        // Calculer les mois travaillés depuis la création du compte
        $dateCreation = Carbon::parse($user->created_at);
        $aujourdhui = Carbon::now();

        $moisTravailles = $dateCreation->diffInMonths($aujourdhui);

        // Solde accumulé : 2 jours par mois
        $soldeAccumule = $moisTravailles * 2;

        // Calculer les congés consommés (uniquement les types déductibles)
        $typesDeductibles = ['congé payé', 'congés payés', 'autre'];

        $congesConsommes = DemandeConge::where('user_id', $user->id_user)
            ->where('statut', 'Approuvé')
            ->whereHas('typeConge', function ($query) use ($typesDeductibles) {
                $query->whereIn(\DB::raw('LOWER(nom_type)'), $typesDeductibles);
            })
            ->sum('nb_jours');

        // Solde disponible
        $soldeDisponible = max(0, $soldeAccumule - $congesConsommes);

        return [
            'monthsWorked' => $moisTravailles,
            'accumulatedLeave' => $soldeAccumule,
            'consumedLeave' => $congesConsommes,
            'availableBalance' => $soldeDisponible,
            'hireDate' => $dateCreation->format('Y-m-d'),
            'currentDate' => $aujourdhui->format('Y-m-d')
        ];
    }

    /**
     * Calculer la progression annuelle des congés
     */
    private function calculerProgressionAnnuelle($user)
    {
        $anneeActuelle = Carbon::now()->year;
        $debutAnnee = Carbon::create($anneeActuelle, 1, 1);
        $finAnnee = Carbon::create($anneeActuelle, 12, 31);
        $aujourdhui = Carbon::now();

        // Calculer le pourcentage de l'année écoulée
        $joursEcoules = $debutAnnee->diffInDays($aujourdhui);
        $totalJoursAnnee = $debutAnnee->diffInDays($finAnnee) + 1;
        $pourcentageAnnee = min(100, round(($joursEcoules / $totalJoursAnnee) * 100, 2));

        // Compter les jours de congés consommés dans l'année en cours
        $congesAnneeEnCours = DemandeConge::where('user_id', $user->id_user)
            ->where('statut', 'Approuvé')
            ->where(function ($query) use ($debutAnnee, $finAnnee) {
                $query->whereBetween('date_debut', [$debutAnnee, $finAnnee])
                    ->orWhereBetween('date_fin', [$debutAnnee, $finAnnee])
                    ->orWhere(function ($q) use ($debutAnnee, $finAnnee) {
                        $q->where('date_debut', '<=', $debutAnnee)
                          ->where('date_fin', '>=', $finAnnee);
                    });
            })
            ->get();

        // Calculer les jours de congés qui sont déjà passés (consommés)
        $joursCongesConsommes = 0;
        $joursCongesTotal = 0;

        foreach ($congesAnneeEnCours as $conge) {
            $dateDebut = Carbon::parse($conge->date_debut);
            $dateFin = Carbon::parse($conge->date_fin);

            // Si le congé est complètement passé
            if ($dateFin->lt($aujourdhui)) {
                $joursCongesConsommes += $conge->nb_jours;
            }
            // Si le congé est en cours
            elseif ($dateDebut->lte($aujourdhui) && $dateFin->gte($aujourdhui)) {
                // Compter seulement les jours jusqu'à aujourd'hui
                $joursCongesConsommes += $this->calculerNombreJours(
                    $dateDebut->format('Y-m-d'),
                    $aujourdhui->format('Y-m-d')
                );
            }

            $joursCongesTotal += $conge->nb_jours;
        }

        // Pourcentage de congés consommés par rapport au total prévu
        $pourcentageCongesConsommes = $joursCongesTotal > 0
            ? min(100, round(($joursCongesConsommes / $joursCongesTotal) * 100, 2))
            : 0;

        return [
            'year' => $anneeActuelle,
            'yearPercentage' => $pourcentageAnnee,
            'daysElapsed' => $joursEcoules,
            'totalDaysInYear' => $totalJoursAnnee,
            'leaveConsumed' => $joursCongesConsommes,
            'leavePlanned' => $joursCongesTotal,
            'leavePercentage' => $pourcentageCongesConsommes
        ];
    }

    /**
     * Convertir le nom du type de congé en clé pour le frontend
     */
    private function getLeaveTypeKey($nomType)
    {
        $nomType = strtolower($nomType);

        $mapping = [
            'congé payé' => 'paid',
            'congés payés' => 'paid',
            'congé maladie' => 'sick',
            'maladie' => 'sick',
            'congé maternité' => 'maternity',
            'maternité' => 'maternity',
            'congé paternité' => 'paternity',
            'paternité' => 'paternity',
            'rtt' => 'rtt',
            'sans solde' => 'unpaid',
            'autre' => 'other'
        ];

        return $mapping[$nomType] ?? 'other';
    }

    /**
     * Calculer le nombre de jours ouvrés (excluant weekends et jours fériés)
     */
    private function calculerNombreJours($dateDebut, $dateFin)
    {
        $debut = Carbon::parse($dateDebut);
        $fin = Carbon::parse($dateFin);
        $jours = 0;

        $joursFeries = array_keys($this->getJoursFeriesComplets());

        while ($debut->lte($fin)) {
            if (!$debut->isWeekend() && !in_array($debut->format('Y-m-d'), $joursFeries)) {
                $jours++;
            }
            $debut->addDay();
        }

        return $jours;
    }

    /**
     * ✅ TOUS LES JOURS FÉRIÉS DU GABON + INTERNATIONAUX (AUTOMATIQUE)
     * Couvre 4 années : année actuelle -1 jusqu'à +2
     */
    private function getJoursFeriesComplets()
    {
        $anneeActuelle = Carbon::now()->year;
        $joursFeries = [];

        for ($annee = $anneeActuelle - 1; $annee <= $anneeActuelle + 2; $annee++) {
            // ========== JOURS FÉRIÉS INTERNATIONAUX ==========
            $joursFeries["{$annee}-01-01"] = "Nouvel An";
            $joursFeries["{$annee}-05-01"] = "Fête du Travail";
            $joursFeries["{$annee}-12-25"] = "Noël";

            // ========== JOURS FÉRIÉS DU GABON ==========
            $joursFeries["{$annee}-03-12"] = "Fête de la Rénovation";
            $joursFeries["{$annee}-04-17"] = "Fête de la Femme gabonaise";
            $joursFeries["{$annee}-05-17"] = "Journée de la Libération";
            $joursFeries["{$annee}-08-16"] = "Commémoration de l'Indépendance (veille)";
            $joursFeries["{$annee}-08-17"] = "Fête de l'Indépendance";
            $joursFeries["{$annee}-11-01"] = "Toussaint";

            // ========== JOURS FÉRIÉS RELIGIEUX (Dates fixes) ==========
            $joursFeries["{$annee}-01-06"] = "Épiphanie";
            $joursFeries["{$annee}-08-15"] = "Assomption";
            $joursFeries["{$annee}-11-02"] = "Fête des Morts";

            // ========== JOURS FÉRIÉS MUSULMANS (Dates variables - approximatives) ==========
            // Aïd el-Fitr (Fin du Ramadan) - varie chaque année
            $aidElFitr = $this->getAidElFitr($annee);
            if ($aidElFitr) {
                $joursFeries[$aidElFitr] = "Aïd el-Fitr (Fin du Ramadan)";
            }

            // Aïd el-Adha (Fête du Sacrifice) - varie chaque année
            $aidElAdha = $this->getAidElAdha($annee);
            if ($aidElAdha) {
                $joursFeries[$aidElAdha] = "Aïd el-Adha (Fête du Sacrifice)";
            }

            // ========== PÂQUES ET JOURS LIÉS (Dates variables) ==========
            $paques = $this->getPaquesDate($annee);
            $joursFeries[$paques] = "Dimanche de Pâques";

            // Lundi de Pâques (+1 jour après Pâques)
            $lundiPaques = Carbon::parse($paques)->addDay()->format('Y-m-d');
            $joursFeries[$lundiPaques] = "Lundi de Pâques";

            // Ascension (+39 jours après Pâques)
            $ascension = Carbon::parse($paques)->addDays(39)->format('Y-m-d');
            $joursFeries[$ascension] = "Ascension";

            // Pentecôte (+50 jours après Pâques)
            $pentecote = Carbon::parse($paques)->addDays(50)->format('Y-m-d');
            $joursFeries[$pentecote] = "Pentecôte";

            // Lundi de Pentecôte (+51 jours après Pâques)
            $lundiPentecote = Carbon::parse($paques)->addDays(51)->format('Y-m-d');
            $joursFeries[$lundiPentecote] = "Lundi de Pentecôte";
        }

        return $joursFeries;
    }

    /**
     * Calculer la date de Pâques (algorithme de Meeus/Jones/Butcher)
     */
    private function getPaquesDate($annee)
    {
        $a = $annee % 19;
        $b = intval($annee / 100);
        $c = $annee % 100;
        $d = intval($b / 4);
        $e = $b % 4;
        $f = intval(($b + 8) / 25);
        $g = intval(($b - $f + 1) / 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intval($c / 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intval(($a + 11 * $h + 22 * $l) / 451);
        $mois = intval(($h + $l - 7 * $m + 114) / 31);
        $jour = (($h + $l - 7 * $m + 114) % 31) + 1;

        return sprintf('%d-%02d-%02d', $annee, $mois, $jour);
    }

    /**
     * Dates approximatives de l'Aïd el-Fitr (Fin du Ramadan)
     * Ces dates varient selon l'observation lunaire
     */
    private function getAidElFitr($annee)
    {
        $dates = [
            2024 => '2024-04-10',
            2025 => '2025-03-30',
            2026 => '2026-03-20',
            2027 => '2027-03-09',
            2028 => '2028-02-26'
        ];

        return $dates[$annee] ?? null;
    }

    /**
     * Dates approximatives de l'Aïd el-Adha (Fête du Sacrifice)
     * Ces dates varient selon l'observation lunaire
     */
    private function getAidElAdha($annee)
    {
        $dates = [
            2024 => '2024-06-16',
            2025 => '2025-06-06',
            2026 => '2026-05-27',
            2027 => '2027-05-16',
            2028 => '2028-05-05'
        ];

        return $dates[$annee] ?? null;
    }

    /**
     * Récupérer les statistiques détaillées par type de congé
     */
    public function getStatistiques()
    {
        try {
            $user = Auth::user();
            $anneeActuelle = Carbon::now()->year;

            // Statistiques par type de congé
            $statistiquesParType = DemandeConge::where('user_id', $user->id_user)
                ->where('statut', 'Approuvé')
                ->whereYear('date_debut', $anneeActuelle)
                ->with('typeConge')
                ->get()
                ->groupBy('typeConge.nom_type')
                ->map(function ($demandes, $typeNom) {
                    $totalJours = $demandes->sum('nb_jours');
                    $nombreDemandes = $demandes->count();
                    $couleur = $demandes->first()->typeConge->couleur ?? '#3b82f6';

                    return [
                        'type' => $typeNom,
                        'totalJours' => $totalJours,
                        'nombreDemandes' => $nombreDemandes,
                        'couleur' => $couleur
                    ];
                });

            // Historique mensuel
            $historiqueMensuel = [];
            for ($mois = 1; $mois <= 12; $mois++) {
                $demandes = DemandeConge::where('user_id', $user->id_user)
                    ->where('statut', 'Approuvé')
                    ->whereYear('date_debut', $anneeActuelle)
                    ->whereMonth('date_debut', $mois)
                    ->sum('nb_jours');

                $historiqueMensuel[] = [
                    'mois' => $mois,
                    'moisNom' => Carbon::create($anneeActuelle, $mois, 1)->locale('fr')->monthName,
                    'joursConges' => $demandes
                ];
            }

            return response()->json([
                'success' => true,
                'statistiquesParType' => $statistiquesParType->values(),
                'historiqueMensuel' => $historiqueMensuel,
                'annee' => $anneeActuelle
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erreur récupération statistiques: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des statistiques'
            ], 500);
        }
    }
}
