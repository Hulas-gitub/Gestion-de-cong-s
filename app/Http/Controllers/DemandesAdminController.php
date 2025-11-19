<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\MailService;

class DemandesAdminController extends Controller
{
    protected $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    /**
     * Page principale de gestion des demandes admin
     */
    public function index()
    {
        return view('admin.demandes-admin');
    }

    /**
     * Récupérer TOUTES les demandes de congés (tous départements)
     * Avec filtres : département, employé, statut
     */
    public function getDemandes(Request $request)
    {
        try {
            $adminId = Auth::id();

            // Vérifier que l'utilisateur est bien admin (role_id = 1)
            $admin = DB::table('users')->where('id_user', $adminId)->first();
            if (!$admin || $admin->role_id != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            // Récupérer les paramètres de filtrage
            $statusFilter = $request->input('filter', 'all'); // all, pending, approved, rejected
            $departmentId = $request->input('department_id', null); // null = tous les départements
            $employeeId = $request->input('employee_id', null); // null = tous les employés

            // Construire la requête de base
            $query = DB::table('demandes_conges as dc')
                ->join('users as u', 'dc.user_id', '=', 'u.id_user')
                ->join('types_conges as tc', 'dc.type_conge_id', '=', 'tc.id_type')
                ->join('departements as d', 'u.departement_id', '=', 'd.id_departement')
                ->leftJoin('users as v', 'dc.validateur_id', '=', 'v.id_user')
                ->select(
                    'dc.*',
                    'u.nom',
                    'u.prenom',
                    'u.email',
                    'u.matricule',
                    'u.photo_url',
                    'u.solde_conges_annuel',
                    'u.conges_pris',
                    'd.nom_departement',
                    'd.couleur_calendrier as departement_couleur',
                    'tc.nom_type as type_conge_nom',
                    'tc.couleur_calendrier',
                    'v.nom as validateur_nom',
                    'v.prenom as validateur_prenom'
                )
                ->orderBy('dc.created_at', 'desc');

            // Filtrer par département si spécifié
            if ($departmentId && $departmentId !== 'all') {
                $query->where('u.departement_id', $departmentId);
            }

            // Filtrer par employé si spécifié
            if ($employeeId && $employeeId !== 'all') {
                $query->where('dc.user_id', $employeeId);
            }

            // Filtrer par statut
            if ($statusFilter !== 'all') {
                switch ($statusFilter) {
                    case 'pending':
                        $query->where('dc.statut', 'En attente');
                        break;
                    case 'approved':
                        $query->where('dc.statut', 'Approuvé');
                        break;
                    case 'rejected':
                        $query->where('dc.statut', 'Refusé');
                        break;
                }
            }

            $demandes = $query->get();

            // Formater les données
            $demandesFormatted = $demandes->map(function ($demande) {
                return [
                    'id' => $demande->id_demande,
                    'employeeName' => $demande->prenom . ' ' . $demande->nom,
                    'employeeEmail' => $demande->email,
                    'employeeMatricule' => $demande->matricule,
                    'employeePhoto' => $demande->photo_url,
                    'department' => $demande->nom_departement,
                    'departmentColor' => $demande->departement_couleur,
                    'leaveType' => $demande->type_conge_nom,
                    'leaveColor' => $demande->couleur_calendrier,
                    'startDate' => $demande->date_debut,
                    'endDate' => $demande->date_fin,
                    'duration' => $demande->nb_jours,
                    'status' => $this->mapStatut($demande->statut),
                    'motif' => $demande->motif,
                    'reason' => $demande->motif,
                    'commentaire_refus' => $demande->commentaire_refus,
                    'document_justificatif' => $demande->document_justificatif,
                    'pdfName' => $demande->document_justificatif ? basename($demande->document_justificatif) : null,
                    'document_de_validation' => $demande->document_de_validation,
                    'remainingBalance' => $demande->solde_conges_annuel - $demande->conges_pris,
                    'submittedTime' => $this->getTimeAgo($demande->created_at),
                    'validateur' => $demande->validateur_nom ? $demande->validateur_prenom . ' ' . $demande->validateur_nom : null,
                    'date_validation' => $demande->date_validation,
                    'avatar' => $this->getAvatarGradient($demande->type_conge_nom)
                ];
            });

            return response()->json([
                'success' => true,
                'demandes' => $demandesFormatted
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des demandes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer tous les départements (pour le menu déroulant)
     */
    public function getAllDepartements()
    {
        try {
            $departements = DB::table('departements')
                ->where('actif', 1)
                ->select('id_departement as id', 'nom_departement as nom', 'couleur_calendrier')
                ->orderBy('nom_departement', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'departements' => $departements
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des départements',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer tous les employés d'un département (pour le menu déroulant)
     */
    public function getEmployeesByDepartement($departementId)
    {
        try {
            $query = DB::table('users')
                ->where('actif', 1)
                ->where('role_id', '!=', 1) // Exclure les admins
                ->select('id_user as id', 'nom', 'prenom', 'email', 'matricule', 'departement_id')
                ->orderBy('nom', 'asc');

            // Si département spécifique
            if ($departementId !== 'all') {
                $query->where('departement_id', $departementId);
            }

            $employees = $query->get();

            return response()->json([
                'success' => true,
                'employees' => $employees
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des employés',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupérer les détails d'une demande spécifique
     */
    public function getDemandeDetails($id)
    {
        try {
            $adminId = Auth::id();

            // Vérifier que l'utilisateur est bien admin
            $admin = DB::table('users')->where('id_user', $adminId)->first();
            if (!$admin || $admin->role_id != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            $demande = DB::table('demandes_conges as dc')
                ->join('users as u', 'dc.user_id', '=', 'u.id_user')
                ->join('types_conges as tc', 'dc.type_conge_id', '=', 'tc.id_type')
                ->join('departements as d', 'u.departement_id', '=', 'd.id_departement')
                ->leftJoin('users as v', 'dc.validateur_id', '=', 'v.id_user')
                ->where('dc.id_demande', $id)
                ->select(
                    'dc.*',
                    'u.nom',
                    'u.prenom',
                    'u.email',
                    'u.matricule',
                    'u.photo_url',
                    'u.solde_conges_annuel',
                    'u.conges_pris',
                    'd.nom_departement',
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
                    'employeeName' => $demande->prenom . ' ' . $demande->nom,
                    'employeeEmail' => $demande->email,
                    'employeeMatricule' => $demande->matricule,
                    'employeePhoto' => $demande->photo_url,
                    'department' => $demande->nom_departement,
                    'leaveType' => $demande->type_conge_nom,
                    'leaveColor' => $demande->couleur_calendrier,
                    'startDate' => $demande->date_debut,
                    'endDate' => $demande->date_fin,
                    'duration' => $demande->nb_jours,
                    'status' => $this->mapStatut($demande->statut),
                    'motif' => $demande->motif,
                    'reason' => $demande->motif,
                    'commentaire_refus' => $demande->commentaire_refus,
                    'document_justificatif' => $demande->document_justificatif,
                    'pdfName' => $demande->document_justificatif ? basename($demande->document_justificatif) : null,
                    'document_de_validation' => $demande->document_de_validation,
                    'remainingBalance' => $demande->solde_conges_annuel - $demande->conges_pris,
                    'submittedTime' => $this->getTimeAgo($demande->created_at),
                    'validateur' => $demande->validateur_nom ? $demande->validateur_prenom . ' ' . $demande->validateur_nom : null,
                    'date_validation' => $demande->date_validation,
                    'avatar' => $this->getAvatarGradient($demande->type_conge_nom)
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
     * Approuver UNE demande de congé
     */
    public function approuverDemande(Request $request, $id)
    {
        try {
            $adminId = Auth::id();

            // Récupérer l'admin connecté
            $admin = DB::table('users')->where('id_user', $adminId)->first();
            if (!$admin || $admin->role_id != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            // Vérifier que la demande existe et est en attente
            $demande = DB::table('demandes_conges as dc')
                ->join('users as u', 'dc.user_id', '=', 'u.id_user')
                ->join('types_conges as tc', 'dc.type_conge_id', '=', 'tc.id_type')
                ->where('dc.id_demande', $id)
                ->where('dc.statut', 'En attente')
                ->select('dc.*', 'u.email', 'u.nom', 'u.prenom', 'tc.nom_type', 'u.id_user')
                ->first();

            if (!$demande) {
                return response()->json([
                    'success' => false,
                    'message' => 'Demande non trouvée ou déjà traitée'
                ], 404);
            }

            // Mettre à jour la demande
            DB::table('demandes_conges')
                ->where('id_demande', $id)
                ->update([
                    'statut' => 'Approuvé',
                    'validateur_id' => $adminId,
                    'date_validation' => now(),
                    'updated_at' => now()
                ]);

            // Mettre à jour le solde de congés de l'employé
            DB::table('users')
                ->where('id_user', $demande->user_id)
                ->increment('conges_pris', $demande->nb_jours);

            // Envoyer l'email de notification via MailService
            $this->mailService->sendEmail(
                $demande->email,
                '✅ Demande de congé approuvée',
                'emails.demande-approuvee',
                [
                    'nom_employe' => $demande->prenom . ' ' . $demande->nom,
                    'nom_chef' => $admin->prenom . ' ' . $admin->nom,
                    'type_conge' => $demande->nom_type,
                    'date_debut' => \Carbon\Carbon::parse($demande->date_debut)->format('d/m/Y'),
                    'date_fin' => \Carbon\Carbon::parse($demande->date_fin)->format('d/m/Y'),
                    'nb_jours' => $demande->nb_jours
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Demande approuvée avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'approbation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Refuser UNE demande de congé
     */
    public function refuserDemande(Request $request, $id)
    {
        try {
            $adminId = Auth::id();

            $request->validate([
                'commentaire_refus' => 'nullable|string|max:500'
            ]);

            // Récupérer l'admin connecté
            $admin = DB::table('users')->where('id_user', $adminId)->first();
            if (!$admin || $admin->role_id != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            // Vérifier que la demande existe et est en attente
            $demande = DB::table('demandes_conges as dc')
                ->join('users as u', 'dc.user_id', '=', 'u.id_user')
                ->join('types_conges as tc', 'dc.type_conge_id', '=', 'tc.id_type')
                ->where('dc.id_demande', $id)
                ->where('dc.statut', 'En attente')
                ->select('dc.*', 'u.email', 'u.nom', 'u.prenom', 'tc.nom_type')
                ->first();

            if (!$demande) {
                return response()->json([
                    'success' => false,
                    'message' => 'Demande non trouvée ou déjà traitée'
                ], 404);
            }

            $commentaire = $request->input('commentaire_refus');

            // Mettre à jour la demande
            DB::table('demandes_conges')
                ->where('id_demande', $id)
                ->update([
                    'statut' => 'Refusé',
                    'validateur_id' => $adminId,
                    'date_validation' => now(),
                    'commentaire_refus' => $commentaire,
                    'updated_at' => now()
                ]);

            // Envoyer l'email de notification via MailService
            $this->mailService->sendEmail(
                $demande->email,
                '❌ Demande de congé refusée',
                'emails.demande-refusee',
                [
                    'nom_employe' => $demande->prenom . ' ' . $demande->nom,
                    'nom_chef' => $admin->prenom . ' ' . $admin->nom,
                    'type_conge' => $demande->nom_type,
                    'date_debut' => \Carbon\Carbon::parse($demande->date_debut)->format('d/m/Y'),
                    'date_fin' => \Carbon\Carbon::parse($demande->date_fin)->format('d/m/Y'),
                    'nb_jours' => $demande->nb_jours,
                    'commentaire' => $commentaire
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Demande refusée avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du refus',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * APPROUVER PLUSIEURS DEMANDES EN MASSE
     */
    public function approuverMultiples(Request $request)
    {
        try {
            $adminId = Auth::id();

            $request->validate([
                'demande_ids' => 'required|array',
                'demande_ids.*' => 'required|integer|exists:demandes_conges,id_demande'
            ]);

            // Récupérer l'admin connecté
            $admin = DB::table('users')->where('id_user', $adminId)->first();
            if (!$admin || $admin->role_id != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            $demandeIds = $request->input('demande_ids');

            // Récupérer toutes les demandes valides (en attente uniquement)
            $demandes = DB::table('demandes_conges as dc')
                ->join('users as u', 'dc.user_id', '=', 'u.id_user')
                ->join('types_conges as tc', 'dc.type_conge_id', '=', 'tc.id_type')
                ->whereIn('dc.id_demande', $demandeIds)
                ->where('dc.statut', 'En attente')
                ->select('dc.*', 'u.email', 'u.nom', 'u.prenom', 'tc.nom_type', 'u.id_user')
                ->get();

            if ($demandes->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune demande valide trouvée'
                ], 404);
            }

            $successCount = 0;
            $errors = [];

            foreach ($demandes as $demande) {
                try {
                    // Mettre à jour la demande
                    DB::table('demandes_conges')
                        ->where('id_demande', $demande->id_demande)
                        ->update([
                            'statut' => 'Approuvé',
                            'validateur_id' => $adminId,
                            'date_validation' => now(),
                            'updated_at' => now()
                        ]);

                    // Mettre à jour le solde de congés
                    DB::table('users')
                        ->where('id_user', $demande->user_id)
                        ->increment('conges_pris', $demande->nb_jours);

                    // Envoyer l'email personnalisé
                    $this->mailService->sendEmail(
                        $demande->email,
                        '✅ Demande de congé approuvée',
                        'emails.demande-approuvee',
                        [
                            'nom_employe' => $demande->prenom . ' ' . $demande->nom,
                            'nom_chef' => $admin->prenom . ' ' . $admin->nom,
                            'type_conge' => $demande->nom_type,
                            'date_debut' => \Carbon\Carbon::parse($demande->date_debut)->format('d/m/Y'),
                            'date_fin' => \Carbon\Carbon::parse($demande->date_fin)->format('d/m/Y'),
                            'nb_jours' => $demande->nb_jours
                        ]
                    );

                    $successCount++;

                } catch (\Exception $e) {
                    $errors[] = "Erreur pour la demande ID {$demande->id_demande}: " . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => "$successCount demande(s) approuvée(s) avec succès",
                'approved_count' => $successCount,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'approbation multiple',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * REFUSER PLUSIEURS DEMANDES EN MASSE
     */
    public function refuserMultiples(Request $request)
    {
        try {
            $adminId = Auth::id();

            $request->validate([
                'demande_ids' => 'required|array',
                'demande_ids.*' => 'required|integer|exists:demandes_conges,id_demande',
                'commentaire_refus' => 'nullable|string|max:500'
            ]);

            // Récupérer l'admin connecté
            $admin = DB::table('users')->where('id_user', $adminId)->first();
            if (!$admin || $admin->role_id != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            $demandeIds = $request->input('demande_ids');
            $commentaire = $request->input('commentaire_refus');

            // Récupérer toutes les demandes valides (en attente uniquement)
            $demandes = DB::table('demandes_conges as dc')
                ->join('users as u', 'dc.user_id', '=', 'u.id_user')
                ->join('types_conges as tc', 'dc.type_conge_id', '=', 'tc.id_type')
                ->whereIn('dc.id_demande', $demandeIds)
                ->where('dc.statut', 'En attente')
                ->select('dc.*', 'u.email', 'u.nom', 'u.prenom', 'tc.nom_type')
                ->get();

            if ($demandes->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune demande valide trouvée'
                ], 404);
            }

            $successCount = 0;
            $errors = [];

            foreach ($demandes as $demande) {
                try {
                    // Mettre à jour la demande
                    DB::table('demandes_conges')
                        ->where('id_demande', $demande->id_demande)
                        ->update([
                            'statut' => 'Refusé',
                            'validateur_id' => $adminId,
                            'date_validation' => now(),
                            'commentaire_refus' => $commentaire,
                            'updated_at' => now()
                        ]);

                    // Envoyer l'email personnalisé
                    $this->mailService->sendEmail(
                        $demande->email,
                        '❌ Demande de congé refusée',
                        'emails.demande-refusee',
                        [
                            'nom_employe' => $demande->prenom . ' ' . $demande->nom,
                            'nom_chef' => $admin->prenom . ' ' . $admin->nom,
                            'type_conge' => $demande->nom_type,
                            'date_debut' => \Carbon\Carbon::parse($demande->date_debut)->format('d/m/Y'),
                            'date_fin' => \Carbon\Carbon::parse($demande->date_fin)->format('d/m/Y'),
                            'nb_jours' => $demande->nb_jours,
                            'commentaire' => $commentaire
                        ]
                    );

                    $successCount++;

                } catch (\Exception $e) {
                    $errors[] = "Erreur pour la demande ID {$demande->id_demande}: " . $e->getMessage();
                }
            }

            return response()->json([
                'success' => true,
                'message' => "$successCount demande(s) refusée(s) avec succès",
                'rejected_count' => $successCount,
                'errors' => $errors
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du refus multiple',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Revalider une demande refusée (remettre en attente)
     */
    public function revaliderDemande($id)
    {
        try {
            $adminId = Auth::id();

            // Vérifier que l'utilisateur est bien admin
            $admin = DB::table('users')->where('id_user', $adminId)->first();
            if (!$admin || $admin->role_id != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            $demande = DB::table('demandes_conges')
                ->where('id_demande', $id)
                ->where('statut', 'Refusé')
                ->first();

            if (!$demande) {
                return response()->json([
                    'success' => false,
                    'message' => 'Demande non trouvée ou non refusée'
                ], 404);
            }

            DB::table('demandes_conges')
                ->where('id_demande', $id)
                ->update([
                    'statut' => 'En attente',
                    'validateur_id' => null,
                    'date_validation' => null,
                    'commentaire_refus' => null,
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Demande remise en attente avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la revalidation',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Supprimer une demande refusée
     */
    public function supprimerDemande($id)
    {
        try {
            $adminId = Auth::id();

            // Vérifier que l'utilisateur est bien admin
            $admin = DB::table('users')->where('id_user', $adminId)->first();
            if (!$admin || $admin->role_id != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            $demande = DB::table('demandes_conges')
                ->where('id_demande', $id)
                ->where('statut', 'Refusé')
                ->first();

            if (!$demande) {
                return response()->json([
                    'success' => false,
                    'message' => 'Demande non trouvée ou non refusée'
                ], 404);
            }

            // Supprimer les documents associés
            if ($demande->document_justificatif) {
                Storage::disk('public')->delete($demande->document_justificatif);
            }
            if ($demande->document_de_validation) {
                Storage::disk('public')->delete($demande->document_de_validation);
            }

            DB::table('demandes_conges')->where('id_demande', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Demande supprimée avec succès'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Téléverser une attestation de validation
     */
    public function uploadAttestation(Request $request, $id)
    {
        try {
            $adminId = Auth::id();

            // Vérifier que l'utilisateur est bien admin
            $admin = DB::table('users')->where('id_user', $adminId)->first();
            if (!$admin || $admin->role_id != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            $request->validate([
                'attestation' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'
            ]);

            $demande = DB::table('demandes_conges as dc')
                ->join('users as u', 'dc.user_id', '=', 'u.id_user')
                ->where('dc.id_demande', $id)
                ->where('dc.statut', 'Approuvé')
                ->select('dc.*', 'u.matricule')
                ->first();

            if (!$demande) {
                return response()->json([
                    'success' => false,
                    'message' => 'Demande non trouvée ou non approuvée'
                ], 404);
            }

            // Supprimer l'ancienne attestation si elle existe
            if ($demande->document_de_validation) {
                Storage::disk('public')->delete($demande->document_de_validation);
            }

            // Sauvegarder le nouveau fichier
            $file = $request->file('attestation');
            $filename = time() . '_' . $demande->matricule . '_attestation.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/attestation', $filename, 'public');

            // Mettre à jour la base de données
            DB::table('demandes_conges')
                ->where('id_demande', $id)
                ->update([
                    'document_de_validation' => $path,
                    'updated_at' => now()
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Attestation téléversée avec succès',
                'path' => $path
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du téléversement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Télécharger un document justificatif
     */
    public function telechargerDocument($id)
    {
        try {
            $adminId = Auth::id();

            // Vérifier que l'utilisateur est bien admin
            $admin = DB::table('users')->where('id_user', $adminId)->first();
            if (!$admin || $admin->role_id != 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            $demande = DB::table('demandes_conges as dc')
                ->join('users as u', 'dc.user_id', '=', 'u.id_user')
                ->where('dc.id_demande', $id)
                ->select('dc.document_justificatif', 'u.nom', 'u.prenom')
                ->first();

            if (!$demande || !$demande->document_justificatif) {
                return response()->json([
                    'success' => false,
                    'message' => 'Document non trouvé'
                ], 404);
            }

            $filePath = storage_path('app/public/' . $demande->document_justificatif);

            if (!file_exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fichier introuvable sur le serveur'
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
     * Visualiser un document justificatif (afficher dans le navigateur)
     */
    public function visualiserDocument($id)
    {
        try {
            $adminId = Auth::id();

            // Vérifier que l'utilisateur est bien admin
            $admin = DB::table('users')->where('id_user', $adminId)->first();
            if (!$admin || $admin->role_id != 1) {
                abort(403, 'Accès non autorisé');
            }

            $demande = DB::table('demandes_conges')
                ->where('id_demande', $id)
                ->select('document_justificatif')
                ->first();

            if (!$demande || !$demande->document_justificatif) {
                abort(404, 'Document non trouvé');
            }

            $filePath = storage_path('app/public/' . $demande->document_justificatif);

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
     * Vérifier si un document justificatif existe
     */
    public function checkDocument($id)
    {
        try {
            $adminId = Auth::id();

            // Vérifier que l'utilisateur est bien admin
            $admin = DB::table('users')->where('id_user', $adminId)->first();
            if (!$admin || $admin->role_id != 1) {
                return response()->json([
                    'success' => false,
                    'hasDocument' => false,
                    'message' => 'Accès non autorisé'
                ], 403);
            }

            $demande = DB::table('demandes_conges')
                ->where('id_demande', $id)
                ->select('document_justificatif')
                ->first();

            if (!$demande) {
                return response()->json([
                    'success' => false,
                    'hasDocument' => false,
                    'message' => 'Demande non trouvée'
                ], 404);
            }

            $hasDocument = !empty($demande->document_justificatif);
            $documentExists = false;
            $documentName = null;
            $documentExtension = null;

            if ($hasDocument) {
                $filePath = storage_path('app/public/' . $demande->document_justificatif);
                $documentExists = file_exists($filePath);
                $documentName = basename($demande->document_justificatif);
                $documentExtension = pathinfo($documentName, PATHINFO_EXTENSION);
            }

            return response()->json([
                'success' => true,
                'hasDocument' => $hasDocument,
                'documentExists' => $documentExists,
                'documentName' => $documentName,
                'documentExtension' => $documentExtension
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la vérification',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ========== MÉTHODES PRIVÉES ==========

    private function mapStatut($statut)
    {
        $map = [
            'En attente' => 'pending',
            'Approuvé' => 'approved',
            'Refusé' => 'rejected'
        ];
        return $map[$statut] ?? 'pending';
    }

    private function getTimeAgo($datetime)
    {
        $now = new \DateTime();
        $ago = new \DateTime($datetime);
        $diff = $now->diff($ago);

        if ($diff->d > 0) {
            return 'il y a ' . $diff->d . ' jour' . ($diff->d > 1 ? 's' : '');
        } elseif ($diff->h > 0) {
            return 'il y a ' . $diff->h . ' heure' . ($diff->h > 1 ? 's' : '');
        } elseif ($diff->i > 0) {
            return 'il y a ' . $diff->i . ' minute' . ($diff->i > 1 ? 's' : '');
        } else {
            return 'à l\'instant';
        }
    }

    private function getAvatarGradient($typeConge)
    {
        $gradients = [
            'Congés payés' => 'from-blue-500 to-purple-500',
            'Congé maladie' => 'from-red-500 to-orange-500',
            'Congé maternité' => 'from-pink-500 to-rose-500',
            'Paternité' => 'from-indigo-500 to-purple-500',
            'Congé sans solde' => 'from-gray-500 to-gray-700',
            'Formation' => 'from-cyan-500 to-blue-500',
        ];

        foreach ($gradients as $key => $gradient) {
            if (stripos($typeConge, $key) !== false) {
                return $gradient;
            }
        }

        return 'from-green-500 to-teal-500';
    }
}
