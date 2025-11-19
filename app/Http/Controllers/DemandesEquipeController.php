<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Services\MailService;

class DemandesEquipeController extends Controller
{
    protected $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    /**
     * Récupérer toutes les demandes de congés du département
     */
    public function index()
    {
        return view('chef-de-departement.demandes-equipe');
    }

    /**
     * Récupérer les demandes de congés des employés du département
     */
    public function getDemande(Request $request)
    {
        try {
            $chefId = Auth::id();

            // Récupérer le département du chef
            $departement = DB::table('users')
                ->where('id_user', $chefId)
                ->value('departement_id');

            if (!$departement) {
                return response()->json([
                    'success' => false,
                    'message' => 'Département non trouvé'
                ], 404);
            }

            $filter = $request->input('filter', 'all');

            // Récupérer les demandes avec les informations des employés
            $query = DB::table('demandes_conges as dc')
                ->join('users as u', 'dc.user_id', '=', 'u.id_user')
                ->join('types_conges as tc', 'dc.type_conge_id', '=', 'tc.id_type')
                ->leftJoin('users as v', 'dc.validateur_id', '=', 'v.id_user')
                ->where('u.departement_id', $departement)
                ->select(
                    'dc.*',
                    'u.nom',
                    'u.prenom',
                    'u.email',
                    'u.matricule',
                    'u.photo_url',
                    'u.solde_conges_annuel',
                    'u.conges_pris',
                    'tc.nom_type as type_conge_nom',
                    'tc.couleur_calendrier',
                    'v.nom as validateur_nom',
                    'v.prenom as validateur_prenom'
                )
                ->orderBy('dc.created_at', 'desc');

            // Filtrer selon le statut
            if ($filter !== 'all') {
                switch ($filter) {
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
     * Récupérer les détails d'une demande spécifique
     */
    public function getDemandeDetails($id)
    {
        try {
            $chefId = Auth::id();

            $demande = DB::table('demandes_conges as dc')
                ->join('users as u', 'dc.user_id', '=', 'u.id_user')
                ->join('types_conges as tc', 'dc.type_conge_id', '=', 'tc.id_type')
                ->join('departements as d', 'u.departement_id', '=', 'd.id_departement')
                ->leftJoin('users as v', 'dc.validateur_id', '=', 'v.id_user')
                ->where('dc.id_demande', $id)
                ->where('d.chef_departement_id', $chefId)
                ->select(
                    'dc.*',
                    'u.nom',
                    'u.prenom',
                    'u.email',
                    'u.matricule',
                    'u.photo_url',
                    'u.solde_conges_annuel',
                    'u.conges_pris',
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
     * Approuver une demande de congé
     */
    public function approuverDemande(Request $request, $id)
    {
        try {
            $chefId = Auth::id();

            // Récupérer le chef connecté
            $chef = DB::table('users')->where('id_user', $chefId)->first();

            // Vérifier que la demande appartient au département du chef
            $demande = DB::table('demandes_conges as dc')
                ->join('users as u', 'dc.user_id', '=', 'u.id_user')
                ->join('departements as d', 'u.departement_id', '=', 'd.id_departement')
                ->join('types_conges as tc', 'dc.type_conge_id', '=', 'tc.id_type')
                ->where('dc.id_demande', $id)
                ->where('d.chef_departement_id', $chefId)
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
                    'validateur_id' => $chefId,
                    'date_validation' => now(),
                    'updated_at' => now()
                ]);

            // Mettre à jour le solde de congés de l'employé
            DB::table('users')
                ->where('id_user', $demande->id_user)
                ->increment('conges_pris', $demande->nb_jours);

            // Envoyer l'email de notification via MailService
            $this->mailService->sendEmail(
                $demande->email,
                '✅ Demande de congé approuvée',
                'emails.demande-approuvee',
                [
                    'nom_employe' => $demande->prenom . ' ' . $demande->nom,
                    'nom_chef' => $chef->prenom . ' ' . $chef->nom,
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
     * Refuser une demande de congé
     */
    public function refuserDemande(Request $request, $id)
    {
        try {
            $chefId = Auth::id();

            $request->validate([
                'commentaire_refus' => 'nullable|string|max:500'
            ]);

            // Récupérer le chef connecté
            $chef = DB::table('users')->where('id_user', $chefId)->first();

            // Vérifier que la demande appartient au département du chef
            $demande = DB::table('demandes_conges as dc')
                ->join('users as u', 'dc.user_id', '=', 'u.id_user')
                ->join('departements as d', 'u.departement_id', '=', 'd.id_departement')
                ->join('types_conges as tc', 'dc.type_conge_id', '=', 'tc.id_type')
                ->where('dc.id_demande', $id)
                ->where('d.chef_departement_id', $chefId)
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
                    'validateur_id' => $chefId,
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
                    'nom_chef' => $chef->prenom . ' ' . $chef->nom,
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
     * Revalider une demande refusée
     */
    public function revaliderDemande($id)
    {
        try {
            $chefId = Auth::id();

            $demande = DB::table('demandes_conges as dc')
                ->join('users as u', 'dc.user_id', '=', 'u.id_user')
                ->join('departements as d', 'u.departement_id', '=', 'd.id_departement')
                ->where('dc.id_demande', $id)
                ->where('d.chef_departement_id', $chefId)
                ->where('dc.statut', 'Refusé')
                ->select('dc.*')
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
            $chefId = Auth::id();

            $demande = DB::table('demandes_conges as dc')
                ->join('users as u', 'dc.user_id', '=', 'u.id_user')
                ->join('departements as d', 'u.departement_id', '=', 'd.id_departement')
                ->where('dc.id_demande', $id)
                ->where('d.chef_departement_id', $chefId)
                ->where('dc.statut', 'Refusé')
                ->select('dc.*')
                ->first();

            if (!$demande) {
                return response()->json([
                    'success' => false,
                    'message' => 'Demande non trouvée ou non refusée'
                ], 404);
            }

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
            $chefId = Auth::id();

            $request->validate([
                'attestation' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120'
            ]);

            $demande = DB::table('demandes_conges as dc')
                ->join('users as u', 'dc.user_id', '=', 'u.id_user')
                ->join('departements as d', 'u.departement_id', '=', 'd.id_departement')
                ->where('dc.id_demande', $id)
                ->where('d.chef_departement_id', $chefId)
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

            // Sauvegarder le nouveau fichier dans le bon dossier
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
        $chefId = Auth::id();

        $demande = DB::table('demandes_conges as dc')
            ->join('users as u', 'dc.user_id', '=', 'u.id_user')
            ->join('departements as d', 'u.departement_id', '=', 'd.id_departement')
            ->where('dc.id_demande', $id)
            ->where('d.chef_departement_id', $chefId)
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
        $chefId = Auth::id();

        $demande = DB::table('demandes_conges as dc')
            ->join('users as u', 'dc.user_id', '=', 'u.id_user')
            ->join('departements as d', 'u.departement_id', '=', 'd.id_departement')
            ->where('dc.id_demande', $id)
            ->where('d.chef_departement_id', $chefId)
            ->select('dc.document_justificatif')
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
        $chefId = Auth::id();

        $demande = DB::table('demandes_conges as dc')
            ->join('users as u', 'dc.user_id', '=', 'u.id_user')
            ->join('departements as d', 'u.departement_id', '=', 'd.id_departement')
            ->where('dc.id_demande', $id)
            ->where('d.chef_departement_id', $chefId)
            ->select('dc.document_justificatif')
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

/**
 * Récupérer la liste des employés du département
 */
public function getEmployees(Request $request)
{
    try {
        $chefId = Auth::id();

        // Récupérer le département du chef
        $departement = DB::table('users')
            ->where('id_user', $chefId)
            ->value('departement_id');

        if (!$departement) {
            return response()->json([
                'success' => false,
                'message' => 'Département non trouvé'
            ], 404);
        }

        // Récupérer tous les employés du département (sauf le chef)
        $employees = DB::table('users')
            ->where('departement_id', $departement)
            ->where('id_user', '!=', $chefId)
            ->select('id_user as id', 'nom', 'prenom', 'email', 'matricule')
            ->orderBy('nom', 'asc')
            ->get();

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
 * Récupérer les demandes de congés des employés du département
 * MODIFIÉ pour supporter le filtre par employé
 */
public function getDemandes(Request $request)
{
    try {
        $chefId = Auth::id();

        // Récupérer le département du chef
        $departement = DB::table('users')
            ->where('id_user', $chefId)
            ->value('departement_id');

        if (!$departement) {
            return response()->json([
                'success' => false,
                'message' => 'Département non trouvé'
            ], 404);
        }

        $filter = $request->input('filter', 'all');
        $employeeId = $request->input('employee_id', null); // Nouveau paramètre

        // Récupérer les demandes avec les informations des employés
        $query = DB::table('demandes_conges as dc')
            ->join('users as u', 'dc.user_id', '=', 'u.id_user')
            ->join('types_conges as tc', 'dc.type_conge_id', '=', 'tc.id_type')
            ->leftJoin('users as v', 'dc.validateur_id', '=', 'v.id_user')
            ->where('u.departement_id', $departement)
            ->select(
                'dc.*',
                'u.nom',
                'u.prenom',
                'u.email',
                'u.matricule',
                'u.photo_url',
                'u.solde_conges_annuel',
                'u.conges_pris',
                'tc.nom_type as type_conge_nom',
                'tc.couleur_calendrier',
                'v.nom as validateur_nom',
                'v.prenom as validateur_prenom'
            )
            ->orderBy('dc.created_at', 'desc');

        // Filtrer par employé si spécifié
        if ($employeeId && $employeeId !== 'all') {
            $query->where('dc.user_id', $employeeId);
        }

        // Filtrer selon le statut
        if ($filter !== 'all') {
            switch ($filter) {
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
