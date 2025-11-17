<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\DemandeConge;
use App\Models\TypeConge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CalendrierEmployesController extends Controller
{
    /**
     * Afficher le calendrier des congés de l'employé
     */
    public function index()
    {
        $user = Auth::user();

        // Récupérer les congés approuvés de l'employé
        $congesApprouves = DemandeConge::where('user_id', $user->id_user)
            ->where('statut', 'Approuvé')
            ->whereHas('typeConge', function ($query) {
                $query->whereIn(DB::raw('LOWER(nom_type)'), ['congé maladie', 'congé paternité', 'congé maternité', 'autre']);
            })
            ->orderBy('date_debut', 'asc')
            ->get();

        // Calculer le solde de congé disponible
        $soldeDisponible = $this->calculerSoldeDisponible($user);

        // Calculer le nombre total de mois travaillés
        $moisTravailles = $this->calculerMoisTravailles($user);

        // Calculer le solde de congé consommé
        $soldeConsomme = $this->calculerSoldeConsomme($user);

        // Calculer le solde de congé cumulé
        $soldeCumule = $this->calculerSoldeCumule($user);

        // Générer la barre de progression pour les congés en cours
        $barreProgression = $this->genererBarreProgression($congesApprouves);

        // Retourner les données pour la vue
        return view('employes.calendrier', compact(
            'congesApprouves',
            'soldeDisponible',
            'moisTravailles',
            'soldeConsomme',
            'soldeCumule',
            'barreProgression'
        ));
    }

    /**
     * Récupérer les données du calendrier en AJAX
     */
    public function getData()
    {
        try {
            $user = Auth::user();

            // Récupérer les congés approuvés
            $congesApprouves = DemandeConge::where('user_id', $user->id_user)
                ->where('statut', 'Approuvé')
                ->whereHas('typeConge', function ($query) {
                    $query->whereIn(DB::raw('LOWER(nom_type)'), ['congé maladie', 'congé paternité', 'congé maternité', 'autre']);
                })
                ->orderBy('date_debut', 'asc')
                ->get();

            // Calculer les soldes et mois travaillés
            $soldeDisponible = $this->calculerSoldeDisponible($user);
            $moisTravailles = $this->calculerMoisTravailles($user);
            $soldeConsomme = $this->calculerSoldeConsomme($user);
            $soldeCumule = $this->calculerSoldeCumule($user);
            $barreProgression = $this->genererBarreProgression($congesApprouves);

            return response()->json([
                'success' => true,
                'congesApprouves' => $congesApprouves,
                'soldeDisponible' => $soldeDisponible,
                'moisTravailles' => $moisTravailles,
                'soldeConsomme' => $soldeConsomme,
                'soldeCumule' => $soldeCumule,
                'barreProgression' => $barreProgression,
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur récupération données calendrier: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des données du calendrier.'
            ], 500);
        }
    }

    /**
     * Calculer le nombre total de mois travaillés depuis la date d'embauche
     */
    private function calculerMoisTravailles($user)
    {
        $dateEmbauche = Carbon::parse($user->created_at);
        $aujourdhui = Carbon::now();
        return $dateEmbauche->diffInMonths($aujourdhui);
    }

    /**
     * Calculer le solde de congé cumulé depuis la création du compte
     */
    private function calculerSoldeCumule($user)
    {
        $moisTravailles = $this->calculerMoisTravailles($user);
        return $moisTravailles * 2; // 2 jours par mois travaillé
    }

    /**
     * Calculer le solde de congé consommé (congés approuvés)
     */
    private function calculerSoldeConsomme($user)
    {
        return DemandeConge::where('user_id', $user->id_user)
            ->where('statut', 'Approuvé')
            ->whereHas('typeConge', function ($query) {
                $query->whereIn(DB::raw('LOWER(nom_type)'), ['congé maladie', 'congé paternité', 'congé maternité', 'autre', 'congé payé', 'congés payés']);
            })
            ->sum('nb_jours');
    }

    /**
     * Calculer le solde de congé disponible
     */
    private function calculerSoldeDisponible($user)
    {
        $soldeCumule = $this->calculerSoldeCumule($user);
        $soldeConsomme = $this->calculerSoldeConsomme($user);
        return max(0, $soldeCumule - $soldeConsomme);
    }

    /**
     * Générer une barre de progression pour les congés en cours
     */
    private function genererBarreProgression($congesApprouves)
    {
        $aujourdhui = Carbon::now();
        $barreProgression = [];

        foreach ($congesApprouves as $conge) {
            $dateDebut = Carbon::parse($conge->date_debut);
            $dateFin = Carbon::parse($conge->date_fin);
            $dureeTotale = $dateDebut->diffInDays($dateFin) + 1; // Inclure le jour de début

            if ($aujourdhui->between($dateDebut, $dateFin)) {
                $joursEcoules = $dateDebut->diffInDays($aujourdhui);
                $progression = ($joursEcoules / $dureeTotale) * 100;
                $barreProgression[] = [
                    'id' => $conge->id_demande,
                    'progression' => round($progression, 2),
                    'dateDebut' => $conge->date_debut,
                    'dateFin' => $conge->date_fin,
                ];
            }
        }

        return $barreProgression;
    }
}
