<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Departement;
use App\Models\DemandeConge;
use App\Models\TypeConge;
use Illuminate\Support\Facades\Storage;

class DashboardEmployesController extends Controller
{
    /**
     * Afficher le dashboard de l'employé
     */
    public function index()
    {
        return view('employes.tableau-de-bord-employers');
    }

    /**
     * Récupérer les statistiques du dashboard
     */
    public function getStatistiques()
    {
        try {
            $employeId = Auth::id();

            // Récupérer le département de l'employé
            $departementId = DB::table('users')
                ->where('id_user', $employeId)
                ->value('departement_id');

            if (!$departementId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Département non trouvé'
                ], 404);
            }

            // Date du premier jour du mois en cours et mois précédent
            $debutMoisCourant = now()->startOfMonth();
            $finMoisCourant = now()->endOfMonth();
            $debutMoisPrecedent = now()->subMonth()->startOfMonth();
            $finMoisPrecedent = now()->subMonth()->endOfMonth();

            // ====== DEMANDES REFUSÉES ======
            $refusesMoisCourant = DB::table('demandes_conges')
                ->where('user_id', $employeId)
                ->where('statut', 'Refusé')
                ->whereBetween('created_at', [$debutMoisCourant, $finMoisCourant])
                ->count();

            $refusesMoisPrecedent = DB::table('demandes_conges')
                ->where('user_id', $employeId)
                ->where('statut', 'Refusé')
                ->whereBetween('created_at', [$debutMoisPrecedent, $finMoisPrecedent])
                ->count();

            $pourcentageRefuses = $this->calculerPourcentage($refusesMoisCourant, $refusesMoisPrecedent);

            // ====== DEMANDES APPROUVÉES ======
            $approuvesMoisCourant = DB::table('demandes_conges')
                ->where('user_id', $employeId)
                ->where('statut', 'Approuvé')
                ->whereBetween('created_at', [$debutMoisCourant, $finMoisCourant])
                ->count();

            $approuvesMoisPrecedent = DB::table('demandes_conges')
                ->where('user_id', $employeId)
                ->where('statut', 'Approuvé')
                ->whereBetween('created_at', [$debutMoisPrecedent, $finMoisPrecedent])
                ->count();

            $pourcentageApprouves = $this->calculerPourcentage($approuvesMoisCourant, $approuvesMoisPrecedent);

            // ====== DEMANDES EN ATTENTE ======
            $enAttente = DB::table('demandes_conges')
                ->where('user_id', $employeId)
                ->where('statut', 'En attente')
                ->count();

            // ====== ÉQUIPE DU DÉPARTEMENT ======
            $equipe = DB::table('users')
                ->where('departement_id', $departementId)
                ->where('actif', 1)
                ->where('id_user', '!=', $employeId) // Exclure l'employé connecté
                ->count();

            // Inclure l'employé connecté dans le compte total
            $equipeTotal = $equipe + 1;

            return response()->json([
                'success' => true,
                'statistiques' => [
                    'equipe_departement' => [
                        'total' => $equipeTotal,
                        'collegues' => $equipe
                    ],
                    'demandes_refusees' => [
                        'mois_courant' => $refusesMoisCourant,
                        'mois_precedent' => $refusesMoisPrecedent,
                        'pourcentage' => $pourcentageRefuses,
                        'tendance' => $this->getTendance($pourcentageRefuses)
                    ],
                    'demandes_approuvees' => [
                        'mois_courant' => $approuvesMoisCourant,
                        'mois_precedent' => $approuvesMoisPrecedent,
                        'pourcentage' => $pourcentageApprouves,
                        'tendance' => $this->getTendance($pourcentageApprouves)
                    ],
                    'en_attente' => [
                        'total' => $enAttente
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des statistiques',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer l'historique des demandes de l'employé connecté
     */
    public function getHistoriqueDemandes(Request $request)
    {
        try {
            $employeId = Auth::id();
            $filter = $request->input('filter', 'tous');

            // Récupérer les demandes de l'employé
            $query = DB::table('demandes_conges as dc')
                ->join('types_conges as tc', 'dc.type_conge_id', '=', 'tc.id_type')
                ->leftJoin('users as v', 'dc.validateur_id', '=', 'v.id_user')
                ->where('dc.user_id', $employeId)
                ->select(
                    'dc.*',
                    'tc.nom_type as type_conge_nom',
                    'tc.couleur_calendrier',
                    'v.nom as validateur_nom',
                    'v.prenom as validateur_prenom'
                )
                ->orderBy('dc.created_at', 'desc');

            // Appliquer les filtres
            if ($filter !== 'tous') {
                switch ($filter) {
                    case 'en_attente':
                        $query->where('dc.statut', 'En attente');
                        break;
                    case 'approuve':
                        $query->where('dc.statut', 'Approuvé');
                        break;
                    case 'refuse':
                        $query->where('dc.statut', 'Refusé');
                        break;
                }
            }

            $demandes = $query->get();

            // Formater les données
            $demandesFormatted = $demandes->map(function ($demande) {
                return [
                    'id' => $demande->id_demande,
                    'type_conge' => $demande->type_conge_nom,
                    'couleur' => $demande->couleur_calendrier,
                    'date_debut' => $demande->date_debut,
                    'date_fin' => $demande->date_fin,
                    'nb_jours' => $demande->nb_jours,
                    'motif' => $demande->motif,
                    'statut' => $this->mapStatut($demande->statut),
                    'statut_label' => $demande->statut,
                    'commentaire_refus' => $demande->commentaire_refus,
                    'document_justificatif' => $demande->document_justificatif,
                    'has_document' => !empty($demande->document_justificatif),
                    'document_name' => $demande->document_justificatif ? basename($demande->document_justificatif) : null,
                    'document_de_validation' => $demande->document_de_validation,
                    'has_attestation' => !empty($demande->document_de_validation),
                    'attestation_name' => $demande->document_de_validation ? basename($demande->document_de_validation) : null,
                    'validateur' => $demande->validateur_nom ? $demande->validateur_prenom . ' ' . $demande->validateur_nom : null,
                    'date_validation' => $demande->date_validation,
                    'created_at' => $demande->created_at,
                    'submitted_time' => $this->getTimeAgo($demande->created_at)
                ];
            });

            return response()->json([
                'success' => true,
                'demandes' => $demandesFormatted,
                'total' => $demandesFormatted->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération de l\'historique',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les détails d'une demande spécifique
     */
    public function getDetailsDemande($id)
    {
        try {
            $employeId = Auth::id();

            $demande = DB::table('demandes_conges as dc')
                ->join('types_conges as tc', 'dc.type_conge_id', '=', 'tc.id_type')
                ->leftJoin('users as v', 'dc.validateur_id', '=', 'v.id_user')
                ->where('dc.id_demande', $id)
                ->where('dc.user_id', $employeId)
                ->select(
                    'dc.*',
                    'tc.nom_type as type_conge_nom',
                    'tc.couleur_calendrier',
                    'v.nom as validateur_nom',
                    'v.prenom as validateur_prenom'
                )
                ->first();

            if (!$demande) {
                return response()->json([
                    'success' => false,
                    'message' => 'Demande non trouvée'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'demande' => [
                    'id' => $demande->id_demande,
                    'type_conge' => $demande->type_conge_nom,
                    'couleur' => $demande->couleur_calendrier,
                    'date_debut' => $demande->date_debut,
                    'date_fin' => $demande->date_fin,
                    'nb_jours' => $demande->nb_jours,
                    'motif' => $demande->motif,
                    'statut' => $this->mapStatut($demande->statut),
                    'statut_label' => $demande->statut,
                    'commentaire_refus' => $demande->commentaire_refus,
                    'document_justificatif' => $demande->document_justificatif,
                    'has_document' => !empty($demande->document_justificatif),
                    'document_name' => $demande->document_justificatif ? basename($demande->document_justificatif) : null,
                    'document_de_validation' => $demande->document_de_validation,
                    'has_attestation' => !empty($demande->document_de_validation),
                    'attestation_name' => $demande->document_de_validation ? basename($demande->document_de_validation) : null,
                    'validateur' => $demande->validateur_nom ? $demande->validateur_prenom . ' ' . $demande->validateur_nom : null,
                    'date_validation' => $demande->date_validation,
                    'created_at' => $demande->created_at,
                    'submitted_time' => $this->getTimeAgo($demande->created_at)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des détails',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les informations du département de l'employé
     */
    public function getDepartementInfo()
    {
        try {
            $employeId = Auth::id();

            $info = DB::table('users as u')
                ->join('departements as d', 'u.departement_id', '=', 'd.id_departement')
                ->leftJoin('users as chef', 'd.chef_departement_id', '=', 'chef.id_user')
                ->where('u.id_user', $employeId)
                ->select(
                    'd.id_departement',
                    'd.nom_departement',
                    'd.description',
                    'd.couleur_calendrier',
                    'chef.nom as chef_nom',
                    'chef.prenom as chef_prenom',
                    'chef.email as chef_email',
                    'chef.photo_url as chef_photo'
                )
                ->first();

            if (!$info) {
                return response()->json([
                    'success' => false,
                    'message' => 'Département non trouvé'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'departement' => [
                    'id' => $info->id_departement,
                    'nom' => $info->nom_departement,
                    'description' => $info->description,
                    'couleur' => $info->couleur_calendrier,
                    'chef' => [
                        'nom_complet' => $info->chef_prenom . ' ' . $info->chef_nom,
                        'email' => $info->chef_email,
                        'photo' => $info->chef_photo
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du département',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les notifications destinées à l'employé
     * Créées par le chef de son département
     */
    public function getNotifications(Request $request)
    {
        try {
            $employeId = Auth::id();
            $limit = $request->input('limit', 5); // Par défaut 5, pour "toutes" mettre null

            // Récupérer le département de l'employé
            $departement = DB::table('users')
                ->where('id_user', $employeId)
                ->select('departement_id')
                ->first();

            if (!$departement || !$departement->departement_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Département non trouvé'
                ], 404);
            }

            // Récupérer l'ID du chef du département
            $chefId = DB::table('departements')
                ->where('id_departement', $departement->departement_id)
                ->value('chef_departement_id');

            if (!$chefId) {
                return response()->json([
                    'success' => true,
                    'notifications' => [],
                    'total' => 0,
                    'message' => 'Aucun chef de département assigné'
                ]);
            }

            // CORRECTION: Récupérer les notifications créées par le chef ET destinées aux employés du département
            // On suppose que le chef crée des notifications pour informer ses employés
            // Deux approches possibles:

            // APPROCHE 1: Les notifications sont créées avec user_id = employé (chaque employé a sa copie)
            // Dans ce cas, on récupère les notifications où user_id = employeId

            // APPROCHE 2: Les notifications sont créées avec user_id = chef (une seule notification pour tous)
            // Dans ce cas, on récupère les notifications où user_id = chefId

            // Basé sur vos données BDD (user_id=22 qui est chef), j'utilise l'APPROCHE 2
            // Mais je vais vous donner les deux versions

            // VERSION FINALE: Récupérer les notifications du chef pour son département
            $query = DB::table('notifications as n')
                ->join('users as u', 'n.user_id', '=', 'u.id_user')
                ->where('n.user_id', $chefId) // Notifications créées par/pour le chef mais visibles par ses employés
                ->select(
                    'n.*',
                    'u.nom as auteur_nom',
                    'u.prenom as auteur_prenom',
                    'u.photo_url as auteur_photo'
                )
                ->orderBy('n.created_at', 'desc');

            // Limiter le nombre de résultats si spécifié
            if ($limit) {
                $query->limit($limit);
            }

            $notifications = $query->get();

            // Formater les données
            $notificationsFormatted = $notifications->map(function ($notif) {
                // Parser document_info si c'est du JSON
                $documentInfo = null;
                if ($notif->document_info) {
                    $documentInfo = json_decode($notif->document_info, true);
                }

                return [
                    'id' => $notif->id_notification,
                    'titre' => $notif->titre,
                    'message' => $notif->message,
                    'type' => $notif->type_notification,
                    'lu' => (bool) $notif->lu,
                    'url_action' => $notif->url_action,
                    'document_info' => $documentInfo,
                    'has_document' => !empty($documentInfo),
                    'auteur' => [
                        'nom_complet' => $notif->auteur_prenom . ' ' . $notif->auteur_nom,
                        'photo' => $notif->auteur_photo
                    ],
                    'created_at' => $notif->created_at,
                    'time_ago' => $this->getTimeAgo($notif->created_at)
                ];
            });

            return response()->json([
                'success' => true,
                'notifications' => $notificationsFormatted,
                'total' => $notificationsFormatted->count()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des notifications',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Marquer une notification comme lue
     */
    public function marquerNotificationLue($id)
    {
        try {
            $employeId = Auth::id();

            // Vérifier que la notification existe et appartient au département de l'employé
            $departementId = DB::table('users')
                ->where('id_user', $employeId)
                ->value('departement_id');

            if (!$departementId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Département non trouvé'
                ], 404);
            }

            $chefId = DB::table('departements')
                ->where('id_departement', $departementId)
                ->value('chef_departement_id');

            $notification = DB::table('notifications')
                ->where('id_notification', $id)
                ->where('user_id', $chefId)
                ->first();

            if (!$notification) {
                return response()->json([
                    'success' => false,
                    'message' => 'Notification non trouvée'
                ], 404);
            }

            DB::table('notifications')
                ->where('id_notification', $id)
                ->update(['lu' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Notification marquée comme lue'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Télécharger un document justificatif (document de l'employé lors de sa demande)
     */
    public function telechargerDocument($id)
    {
        try {
            $employeId = Auth::id();

            $demande = DB::table('demandes_conges')
                ->where('id_demande', $id)
                ->where('user_id', $employeId)
                ->select('document_justificatif')
                ->first();

            if (!$demande || !$demande->document_justificatif) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document non trouvé'
                ], 404);
            }

            // Construction du chemin complet
            $filePath = public_path('storage/' . $demande->document_justificatif);

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fichier introuvable sur le serveur',
                    'path_checked' => $filePath
                ], 404);
            }

            $fileName = basename($demande->document_justificatif);

            return response()->download($filePath, $fileName);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du téléchargement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Visualiser un document justificatif dans le navigateur
     */
    public function visualiserDocument($id)
    {
        try {
            $employeId = Auth::id();

            $demande = DB::table('demandes_conges')
                ->where('id_demande', $id)
                ->where('user_id', $employeId)
                ->select('document_justificatif')
                ->first();

            if (!$demande || !$demande->document_justificatif) {
                abort(404, 'Document non trouvé');
            }

            $filePath = public_path('storage/' . $demande->document_justificatif);

            if (!file_exists($filePath)) {
                abort(404, 'Fichier introuvable sur le serveur');
            }

            $mimeType = mime_content_type($filePath);

            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline'
            ]);

        } catch (\Exception $e) {
            abort(500, 'Erreur lors de la visualisation du document');
        }
    }

    /**
     * Télécharger une attestation de validation
     */
    public function telechargerAttestation($id)
    {
        try {
            $employeId = Auth::id();

            $demande = DB::table('demandes_conges')
                ->where('id_demande', $id)
                ->where('user_id', $employeId)
                ->select('document_de_validation')
                ->first();

            if (!$demande || !$demande->document_de_validation) {
                return response()->json([
                    'success' => false,
                    'message' => 'Attestation non trouvée'
                ], 404);
            }

            $filePath = storage_path('app/public/' . $demande->document_de_validation);

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fichier introuvable sur le serveur'
                ], 404);
            }

            $fileName = basename($demande->document_de_validation);

            return response()->download($filePath, $fileName);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du téléchargement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Visualiser une attestation de validation
     */
    public function visualiserAttestation($id)
    {
        try {
            $employeId = Auth::id();

            $demande = DB::table('demandes_conges')
                ->where('id_demande', $id)
                ->where('user_id', $employeId)
                ->select('document_de_validation')
                ->first();

            if (!$demande || !$demande->document_de_validation) {
                abort(404, 'Attestation non trouvée');
            }

            $filePath = storage_path('app/public/' . $demande->document_de_validation);

            if (!file_exists($filePath)) {
                abort(404, 'Fichier introuvable sur le serveur');
            }

            $mimeType = mime_content_type($filePath);

            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline'
            ]);

        } catch (\Exception $e) {
            abort(500, 'Erreur lors de la visualisation de l\'attestation');
        }
    }

    /**
     * Télécharger un document de notification (document du chef de département)
     */
    public function telechargerDocumentNotification($id)
    {
        try {
            $employeId = Auth::id();

            // Vérifier que la notification appartient au département
            $departementId = DB::table('users')
                ->where('id_user', $employeId)
                ->value('departement_id');

            if (!$departementId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Département non trouvé'
                ], 404);
            }

            $chefId = DB::table('departements')
                ->where('id_departement', $departementId)
                ->value('chef_departement_id');

            $notification = DB::table('notifications')
                ->where('id_notification', $id)
                ->where('user_id', $chefId)
                ->select('document_info')
                ->first();

            if (!$notification || !$notification->document_info) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document non trouvé'
                ], 404);
            }

            $documentInfo = json_decode($notification->document_info, true);

            // CORRECTION: Utiliser 'chemin' au lieu de 'path'
            if (!isset($documentInfo['chemin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chemin du document invalide'
                ], 404);
            }

            // Construction du chemin complet
            $filePath = public_path('storage/' . $documentInfo['chemin']);

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fichier introuvable sur le serveur',
                    'path_checked' => $filePath
                ], 404);
            }

            // CORRECTION: Utiliser 'nom_fichier' au lieu de 'name'
            $fileName = $documentInfo['nom_fichier'] ?? basename($documentInfo['chemin']);

            return response()->download($filePath, $fileName);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du téléchargement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Visualiser un document de notification dans le navigateur
     */
    public function visualiserDocumentNotification($id)
    {
        try {
            $employeId = Auth::id();

            // Vérifier que la notification appartient au département
            $departementId = DB::table('users')
                ->where('id_user', $employeId)
                ->value('departement_id');

            if (!$departementId) {
                abort(404, 'Département non trouvé');
            }

            $chefId = DB::table('departements')
                ->where('id_departement', $departementId)
                ->value('chef_departement_id');

            $notification = DB::table('notifications')
                ->where('id_notification', $id)
                ->where('user_id', $chefId)
                ->select('document_info')
                ->first();

            if (!$notification || !$notification->document_info) {
                abort(404, 'Document non trouvé');
            }

            $documentInfo = json_decode($notification->document_info, true);

            if (!isset($documentInfo['chemin'])) {
                abort(404, 'Chemin du document invalide');
            }

            $filePath = public_path('storage/' . $documentInfo['chemin']);

            if (!file_exists($filePath)) {
                abort(404, 'Fichier introuvable sur le serveur');
            }

            $mimeType = mime_content_type($filePath);

            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline'
            ]);

        } catch (\Exception $e) {
            abort(500, 'Erreur lors de la visualisation du document');
        }
    }

    // ========== MÉTHODES PRIVÉES ==========

    /**
     * Mapper le statut de la base de données vers le format frontend
     */
    private function mapStatut($statut)
    {
        $map = [
            'En attente' => 'pending',
            'Approuvé' => 'approved',
            'Refusé' => 'rejected'
        ];
        return $map[$statut] ?? 'pending';
    }

    /**
     * Calculer le pourcentage de variation entre deux valeurs
     */
    private function calculerPourcentage($valeurActuelle, $valeurPrecedente)
    {
        // Si pas de valeur précédente
        if ($valeurPrecedente == 0) {
            // Si valeur actuelle > 0, c'est une augmentation de 100%
            if ($valeurActuelle > 0) {
                return 100;
            }
            // Sinon pas de changement
            return 0;
        }

        // Calculer la variation en pourcentage
        $variation = (($valeurActuelle - $valeurPrecedente) / $valeurPrecedente) * 100;

        return round($variation, 1); // Arrondi à 1 décimale
    }

    /**
     * Déterminer la tendance (hausse, baisse, stable)
     */
    private function getTendance($pourcentage)
    {
        if ($pourcentage > 0) {
            return 'hausse';
        } elseif ($pourcentage < 0) {
            return 'baisse';
        } else {
            return 'stable';
        }
    }

    /**
     * Calculer le temps écoulé depuis une date
     */
    private function getTimeAgo($datetime)
    {
        if (!$datetime) {
            return 'Date inconnue';
        }

        $now = new \DateTime();
        $ago = new \DateTime($datetime);
        $diff = $now->diff($ago);

        if ($diff->y > 0) {
            return 'il y a ' . $diff->y . ' an' . ($diff->y > 1 ? 's' : '');
        } elseif ($diff->m > 0) {
            return 'il y a ' . $diff->m . ' mois';
        } elseif ($diff->d > 0) {
            return 'il y a ' . $diff->d . ' jour' . ($diff->d > 1 ? 's' : '');
        } elseif ($diff->h > 0) {
            return 'il y a ' . $diff->h . ' heure' . ($diff->h > 1 ? 's' : '');
        } elseif ($diff->i > 0) {
            return 'il y a ' . $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
        } else {
            return 'à l\'instant';
        }
    }
}
