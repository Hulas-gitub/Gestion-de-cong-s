<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Departement;
use App\Models\DemandeConge;
use App\Models\TypeConge;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class DemandesEmployesController extends Controller
{
    protected $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;

        // Créer le dossier uploads/justificatifs s'il n'existe pas
        if (!Storage::disk('public')->exists('uploads/justificatifs')) {
            Storage::disk('public')->makeDirectory('uploads/justificatifs');
        }
    }

    /**
     * Afficher la page des congés de l'employé avec ses demandes
     */
    public function index()
    {
        $user = Auth::user();
        // Récupérer toutes les demandes de l'employé
        $demandes = DemandeConge::where('user_id', $user->id_user)
            ->with(['validateur', 'typeConge'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculer le solde disponible
        $soldeDisponible = $this->calculerSoldeDisponible($user);

        // Si la requête est AJAX, retourner du JSON
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'demandes' => $demandes,
                'soldeDisponible' => $soldeDisponible
            ]);
        }

        // Sinon, retourner la vue normale
        $roles = Role::all();
        $allDepartements = Departement::all();
        $users = User::all();
        $typesConges = TypeConge::where('actif', 1)->get();

        return view('employes.conges-employers', compact(
            'demandes',
            'soldeDisponible',
            'roles',
            'allDepartements',
            'users',
            'typesConges'
        ));
    }

    /**
     * Récupérer les données des demandes en AJAX
     */
    public function getData()
    {
        try {
            $user = Auth::user();

            // Récupérer toutes les demandes de l'employé
            $demandes = DemandeConge::where('user_id', $user->id_user)
                ->with(['validateur', 'typeConge'])
                ->orderBy('created_at', 'desc')
                ->get();

            // Calculer le solde disponible
            $soldeDisponible = $this->calculerSoldeDisponible($user);

            return response()->json([
                'success' => true,
                'demandes' => $demandes,
                'soldeDisponible' => $soldeDisponible
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur récupération données demandes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des données'
            ], 500);
        }
    }

    /**
     * Créer une nouvelle demande de congé
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            // ✅ VÉRIFICATION 0 : Vérifier si l'utilisateur a déjà une demande en cours
            $demandeEnCours = DemandeConge::where('user_id', $user->id_user)
                ->where(function ($query) {
                    $query->where('statut', 'En attente')
                          ->orWhere(function ($q) {
                              $q->where('statut', 'Approuvé')
                                ->where('date_fin', '>=', Carbon::now()->format('Y-m-d'));
                          });
                })
                ->first();

            if ($demandeEnCours) {
                $messageStatut = $demandeEnCours->statut === 'En attente'
                    ? 'en attente de validation'
                    : 'approuvée et en cours (jusqu\'au ' . Carbon::parse($demandeEnCours->date_fin)->format('d/m/Y') . ')';

                return response()->json([
                    'success' => false,
                    'message' => "❌ Erreur : Vous avez déjà effectué une demande qui est {$messageStatut}. Vous devez attendre que cette demande soit traitée et terminée avant de pouvoir en faire une nouvelle."
                ], 422);
            }

            // ✅ VALIDATION avec messages personnalisés
            $validated = $request->validate([
                'type_conge_id' => 'required|exists:types_conges,id_type',
                'date_debut' => 'required|date|after_or_equal:today',
                'date_fin' => 'required|date|after_or_equal:date_debut',
                'motif' => 'nullable|string|max:1000',
                'document_justificatif' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240' // 10MB max
            ], [
                'type_conge_id.required' => 'Le type de congé est obligatoire',
                'type_conge_id.exists' => 'Le type de congé sélectionné n\'existe pas',
                'date_debut.required' => 'La date de début est obligatoire',
                'date_debut.after_or_equal' => 'La date de début doit être aujourd\'hui ou dans le futur',
                'date_fin.required' => 'La date de fin est obligatoire',
                'date_fin.after_or_equal' => 'La date de fin doit être après ou égale à la date de début',
                'document_justificatif.mimes' => 'Le document doit être au format: PDF, DOC, DOCX, JPG, JPEG ou PNG',
                'document_justificatif.max' => 'Le document ne doit pas dépasser 10 MB'
            ]);

            // Récupérer le type de congé
            $typeConge = TypeConge::find($validated['type_conge_id']);

            if (!$typeConge) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Type de congé introuvable.'
                ], 422);
            }

            $typeCongeNom = strtolower($typeConge->nom_type);

            // ✅ CALCUL DU NOMBRE DE JOURS : TOUS LES JOURS CALENDAIRES (weekends inclus)
            $nbJours = $this->calculerNombreJoursCalendaires($validated['date_debut'], $validated['date_fin']);

            if ($nbJours <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ La période sélectionnée est invalide.'
                ], 422);
            }

            // ✅ VÉRIFICATION 1 : Vérifier le solde UNIQUEMENT pour congés payés (AVANT toute autre vérification)
            if ($typeCongeNom === 'congé payé' || $typeCongeNom === 'congés payés') {
                $soldeDisponible = $this->calculerSoldeDisponible($user);

                // Vérifier si l'employé a accumulé au moins 2 jours (1 mois de travail)
                if ($soldeDisponible < 2) {
                    return response()->json([
                        'success' => false,
                        'message' => '❌ Vous devez travailler au moins 1 mois complet pour accumuler des jours de congé. Actuellement, vous avez ' . $soldeDisponible . ' jour(s) disponible(s).'
                    ], 422);
                }

                // ✅ VÉRIFICATION STRICTE : Le nombre de jours demandés ne doit PAS dépasser le solde
                if ($nbJours > $soldeDisponible) {
                    return response()->json([
                        'success' => false,
                        'message' => "❌ Solde insuffisant. Vous disposez de {$soldeDisponible} jour(s) disponible(s) et vous demandez {$nbJours} jour(s). Vous ne pouvez pas faire une demande supérieure à votre solde."
                    ], 422);
                }
            }

            // ✅ VÉRIFICATION 2 : Délai de préavis de 21 jours (UNIQUEMENT pour congé payé)
            if ($typeCongeNom === 'congé payé' || $typeCongeNom === 'congés payés') {
                $dateDebut = Carbon::parse($validated['date_debut']);
                $aujourdhui = Carbon::now();
                $joursAvance = $aujourdhui->diffInDays($dateDebut, false);

                if ($joursAvance < 21) {
                    return response()->json([
                        'success' => false,
                        'message' => '❌ Délai de préavis insuffisant. Pour un congé payé, vous devez faire votre demande au moins 21 jours avant le début du congé.'
                    ], 422);
                }
            }

            // ✅ VÉRIFICATION 3 : Limite de 5 jours pour le type "Autre"
            if ($typeCongeNom === 'autre' && $nbJours > 5) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Le congé "Autre" est limité à 5 jours maximum par demande.'
                ], 422);
            }

            // ✅ VÉRIFICATION 4 : Vérifier le chevauchement de dates
            $chevauchement = DemandeConge::where('user_id', $user->id_user)
                ->whereIn('statut', ['En attente', 'Approuvé']) // Exclure Refusé et Annulé
                ->where(function ($query) use ($validated) {
                    $query->whereBetween('date_debut', [$validated['date_debut'], $validated['date_fin']])
                        ->orWhereBetween('date_fin', [$validated['date_debut'], $validated['date_fin']])
                        ->orWhere(function ($q) use ($validated) {
                            $q->where('date_debut', '<=', $validated['date_debut'])
                              ->where('date_fin', '>=', $validated['date_fin']);
                        });
                })
                ->exists();

            if ($chevauchement) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Vous avez déjà une demande de congé sur cette période.'
                ], 422);
            }

            // ✅ VÉRIFICATION 5 : Vérifier le quota de congés simultanés du département (30%)
            if (!$this->verifierQuotaDepartement($user->departement_id, $validated['date_debut'], $validated['date_fin'])) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Le quota de congés simultanés (30% max) est atteint pour cette période dans votre département. Veuillez choisir d\'autres dates.'
                ], 422);
            }

            // ✅ UPLOAD DU JUSTIFICATIF : Stocké dans uploads/justificatifs
            $documentPath = null;
            if ($request->hasFile('document_justificatif')) {
                $file = $request->file('document_justificatif');
                $filename = time() . '_' . $user->matricule . '_' . $file->getClientOriginalName();
                $documentPath = $file->storeAs('uploads/justificatifs', $filename, 'public');

                Log::info('📎 Document uploadé avec succès', [
                    'fichier' => $filename,
                    'chemin' => $documentPath,
                    'taille' => $file->getSize()
                ]);
            }

            // ✅ CRÉER LA DEMANDE
            $demande = DemandeConge::create([
                'user_id' => $user->id_user,
                'type_conge_id' => $validated['type_conge_id'],
                'date_debut' => $validated['date_debut'],
                'date_fin' => $validated['date_fin'],
                'nb_jours' => $nbJours,
                'motif' => $validated['motif'] ?? null,
                'statut' => 'En attente',
                'document_justificatif' => $documentPath
            ]);

            Log::info('✅ Demande de congé créée', [
                'demande_id' => $demande->id_demande,
                'employe' => $user->email,
                'type' => $typeConge->nom_type,
                'nb_jours' => $nbJours
            ]);

            // ✅ ENVOYER EMAIL AU CHEF DE DÉPARTEMENT (AVEC VÉRIFICATION)
            $emailEnvoye = $this->envoyerNotificationChef($demande, $user);

            return response()->json([
                'success' => true,
                'message' => $emailEnvoye
                    ? '✅ Votre demande de congé a été soumise avec succès ! Un email a été envoyé à votre chef de département.'
                    : '⚠️ Votre demande a été créée mais l\'email n\'a pas pu être envoyé au chef. Veuillez le contacter directement.',
                'demande' => $demande,
                'email_envoye' => $emailEnvoye
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation échouée:', ['errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => '❌ Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur création demande congé: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => '❌ Une erreur est survenue lors de la création de votre demande: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Relancer une demande refusée
     */
    public function relancer($id)
    {
        try {
            $user = Auth::user();

            // Récupérer la demande refusée
            $demande = DemandeConge::where('id_demande', $id)
                ->where('user_id', $user->id_user)
                ->where('statut', 'Refusé')
                ->firstOrFail();

            // Vérifier que le congé n'a pas encore commencé
            $dateDebut = Carbon::parse($demande->date_debut);
            $aujourdhui = Carbon::now();

            if ($aujourdhui->gte($dateDebut)) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Impossible de relancer une demande dont le congé a déjà commencé. Veuillez créer une nouvelle demande.'
                ], 422);
            }

            // Récupérer le type de congé
            $typeConge = $demande->typeConge;
            $typeCongeNom = strtolower($typeConge->nom_type);

            // Vérifier le délai de 7 jours pour congé payé
            if ($typeCongeNom === 'congé payé' || $typeCongeNom === 'congés payés') {
                $joursAvance = $aujourdhui->diffInDays($dateDebut, false);

                if ($joursAvance < 7) {
                    return response()->json([
                        'success' => false,
                        'message' => '❌ Délai de préavis insuffisant. Pour relancer un congé payé, la date de début doit être au moins 7 jours après aujourd\'hui.'
                    ], 422);
                }
            }

            // Vérifier le solde pour congés payés
            if ($typeCongeNom === 'congé payé' || $typeCongeNom === 'congés payés') {
                $soldeDisponible = $this->calculerSoldeDisponible($user);

                if ($soldeDisponible < 2) {
                    return response()->json([
                        'success' => false,
                        'message' => '❌ Vous devez travailler au moins 1 mois complet pour accumuler des jours de congé.'
                    ], 422);
                }

                if ($demande->nb_jours > $soldeDisponible) {
                    return response()->json([
                        'success' => false,
                        'message' => "❌ Solde insuffisant. Vous disposez de {$soldeDisponible} jour(s) et cette demande nécessite {$demande->nb_jours} jour(s)."
                    ], 422);
                }
            }

            // Vérifier le chevauchement avec d'autres demandes
            $chevauchement = DemandeConge::where('user_id', $user->id_user)
                ->where('id_demande', '!=', $id) // Exclure la demande actuelle
                ->whereIn('statut', ['En attente', 'Approuvé'])
                ->where(function ($query) use ($demande) {
                    $query->whereBetween('date_debut', [$demande->date_debut, $demande->date_fin])
                        ->orWhereBetween('date_fin', [$demande->date_debut, $demande->date_fin])
                        ->orWhere(function ($q) use ($demande) {
                            $q->where('date_debut', '<=', $demande->date_debut)
                              ->where('date_fin', '>=', $demande->date_fin);
                        });
                })
                ->exists();

            if ($chevauchement) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Vous avez déjà une demande de congé sur cette période.'
                ], 422);
            }

            // Vérifier le quota de congés simultanés
            if (!$this->verifierQuotaDepartement($user->departement_id, $demande->date_debut, $demande->date_fin, $demande->id_demande)) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Le quota de congés simultanés (30% max) est atteint pour cette période dans votre département.'
                ], 422);
            }

            // Remettre la demande en attente
            $demande->update([
                'statut' => 'En attente',
                'validateur_id' => null,
                'date_validation' => null,
                'motif_refus' => null
            ]);

            // Notifier le chef de département
            $this->envoyerNotificationChef($demande, $user);

            return response()->json([
                'success' => true,
                'message' => '✅ Votre demande a été relancée avec succès ! Un email a été envoyé à votre chef de département.'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur relance demande: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '❌ Une erreur est survenue lors de la relance de votre demande.'
            ], 500);
        }
    }

    /**
     * Télécharger un document justificatif
     */
    public function telechargerDocument($id)
    {
        try {
            $user = Auth::user();

            // Récupérer la demande (l'employé peut télécharger ses propres documents)
            $demande = DemandeConge::where('id_demande', $id)
                ->where('user_id', $user->id_user)
                ->firstOrFail();

            // Vérifier si un document existe
            if (!$demande->document_justificatif) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Aucun document justificatif n\'est attaché à cette demande.'
                ], 404);
            }

            // Vérifier si le fichier existe physiquement
            if (!Storage::disk('public')->exists($demande->document_justificatif)) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Le document n\'existe plus sur le serveur.'
                ], 404);
            }

            // Récupérer le chemin complet du fichier
            $filePath = Storage::disk('public')->path($demande->document_justificatif);

            // Récupérer le nom original du fichier
            $fileName = basename($demande->document_justificatif);

            // Log du téléchargement
            Log::info('📥 Téléchargement de document', [
                'employe' => $user->email,
                'demande_id' => $id,
                'fichier' => $fileName
            ]);

            // Retourner le fichier en téléchargement
            return response()->download($filePath, $fileName);

        } catch (\Exception $e) {
            Log::error('❌ Erreur téléchargement document: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '❌ Une erreur est survenue lors du téléchargement.'
            ], 500);
        }
    }

    /**
     * Supprimer une demande de congé
     */
    public function supprimer($id)
    {
        try {
            $user = Auth::user();
            $demande = DemandeConge::where('id_demande', $id)
                ->where('user_id', $user->id_user)
                ->firstOrFail();

            // Vérifier que le congé n'a pas encore commencé
            $dateDebut = Carbon::parse($demande->date_debut);
            $aujourdhui = Carbon::now();

            if ($aujourdhui->gte($dateDebut)) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Impossible de supprimer une demande dont le congé a déjà commencé.'
                ], 422);
            }

            // Vérifier que la demande peut être supprimée (statut En attente ou Refusé uniquement)
            if (!in_array($demande->statut, ['En attente', 'Refusé'])) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Seules les demandes "En attente" ou "Refusées" peuvent être supprimées.'
                ], 422);
            }

            // Supprimer le fichier justificatif s'il existe
            if ($demande->document_justificatif && Storage::disk('public')->exists($demande->document_justificatif)) {
                Storage::disk('public')->delete($demande->document_justificatif);
            }

            // Supprimer la demande de la base de données
            $demande->delete();

            // Notifier le chef
            $departement = Departement::find($user->departement_id);
            if ($departement && $departement->chef_departement_id) {
                $chef = User::find($departement->chef_departement_id);
                if ($chef && $chef->email) {
                    Log::info('Demande supprimée par l\'employé', [
                        'employe' => $user->email,
                        'demande_id' => $id,
                        'chef' => $chef->email
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => '✅ Votre demande a été supprimée avec succès.'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur suppression demande: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '❌ Une erreur est survenue lors de la suppression.'
            ], 500);
        }
    }

    /**
     * Modifier une demande de congé
     */
    public function modifier(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $demande = DemandeConge::where('id_demande', $id)
                ->where('user_id', $user->id_user)
                ->firstOrFail();

            // Vérifier que le congé n'a pas encore commencé
            $dateDebut = Carbon::parse($demande->date_debut);
            $aujourdhui = Carbon::now();

            if ($aujourdhui->gte($dateDebut)) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Impossible de modifier une demande dont le congé a déjà commencé.'
                ], 422);
            }

            // ✅ VALIDATION AVEC DOCUMENT (optionnel)
            $validated = $request->validate([
                'date_debut' => 'required|date|after_or_equal:today',
                'date_fin' => 'required|date|after_or_equal:date_debut',
                'motif' => 'nullable|string|max:1000',
                'document_justificatif' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:10240'
            ], [
                'date_debut.required' => 'La date de début est obligatoire',
                'date_debut.after_or_equal' => 'La date de début doit être aujourd\'hui ou dans le futur',
                'date_fin.required' => 'La date de fin est obligatoire',
                'date_fin.after_or_equal' => 'La date de fin doit être après ou égale à la date de début',
                'document_justificatif.mimes' => 'Le document doit être au format: PDF, DOC, DOCX, JPG, JPEG ou PNG',
                'document_justificatif.max' => 'Le document ne doit pas dépasser 10 MB'
            ]);

            // Récupérer le type de congé
            $typeConge = $demande->typeConge;
            $typeCongeNom = strtolower($typeConge->nom_type);

            // Vérifier le délai de 7 jours pour congé payé
            if ($typeCongeNom === 'congé payé' || $typeCongeNom === 'congés payés') {
                $nouvelleDateDebut = Carbon::parse($validated['date_debut']);
                $joursAvance = $aujourdhui->diffInDays($nouvelleDateDebut, false);

                if ($joursAvance < 7) {
                    return response()->json([
                        'success' => false,
                        'message' => '❌ Délai de préavis insuffisant. La nouvelle date doit être au moins 7 jours après aujourd\'hui.'
                    ], 422);
                }
            }

            // Recalculer le nombre de jours (TOUS LES JOURS CALENDAIRES)
            $nbJours = $this->calculerNombreJoursCalendaires($validated['date_debut'], $validated['date_fin']);

            if ($nbJours <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ La période sélectionnée est invalide.'
                ], 422);
            }

            // Vérifier la limite de 5 jours pour "Autre"
            if ($typeCongeNom === 'autre' && $nbJours > 5) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Le congé "Autre" est limité à 5 jours maximum.'
                ], 422);
            }

            // Vérifier le solde pour congés payés et "Autre"
            $typesDeductibles = ['congé payé', 'congés payés', 'autre'];
            if (in_array($typeCongeNom, $typesDeductibles)) {
                $soldeDisponible = $this->calculerSoldeDisponible($user);

                // Ajouter les jours de la demande actuelle au solde
                $soldeAvecDemandeActuelle = $soldeDisponible + $demande->nb_jours;

                if ($soldeAvecDemandeActuelle < 2) {
                    return response()->json([
                        'success' => false,
                        'message' => '❌ Vous devez travailler au moins 1 mois complet pour accumuler des jours de congé.'
                    ], 422);
                }

                if ($nbJours > $soldeAvecDemandeActuelle) {
                    return response()->json([
                        'success' => false,
                        'message' => "❌ Solde insuffisant. Vous disposez de {$soldeAvecDemandeActuelle} jour(s)."
                    ], 422);
                }
            }

            // Vérifier le chevauchement avec d'autres demandes
            $chevauchement = DemandeConge::where('user_id', $user->id_user)
                ->where('id_demande', '!=', $id)
                ->whereIn('statut', ['En attente', 'Approuvé'])
                ->where(function ($query) use ($validated) {
                    $query->whereBetween('date_debut', [$validated['date_debut'], $validated['date_fin']])
                        ->orWhereBetween('date_fin', [$validated['date_debut'], $validated['date_fin']])
                        ->orWhere(function ($q) use ($validated) {
                            $q->where('date_debut', '<=', $validated['date_debut'])
                              ->where('date_fin', '>=', $validated['date_fin']);
                        });
                })
                ->exists();

            if ($chevauchement) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Vous avez déjà une demande de congé sur cette période.'
                ], 422);
            }

            // Vérifier le quota de congés simultanés
            if (!$this->verifierQuotaDepartement($user->departement_id, $validated['date_debut'], $validated['date_fin'], $demande->id_demande)) {
                return response()->json([
                    'success' => false,
                    'message' => '❌ Le quota de congés simultanés est atteint pour cette période.'
                ], 422);
            }

            // ✅ GESTION DU NOUVEAU DOCUMENT JUSTIFICATIF
            $documentPath = $demande->document_justificatif; // Garder l'ancien par défaut

            if ($request->hasFile('document_justificatif')) {
                // Supprimer l'ancien document s'il existe
                if ($demande->document_justificatif && Storage::disk('public')->exists($demande->document_justificatif)) {
                    Storage::disk('public')->delete($demande->document_justificatif);
                    Log::info('🗑️ Ancien document supprimé', ['chemin' => $demande->document_justificatif]);
                }

                // Uploader le nouveau document
                $file = $request->file('document_justificatif');
                $filename = time() . '_' . $user->matricule . '_' . $file->getClientOriginalName();
                $documentPath = $file->storeAs('uploads/justificatifs', $filename, 'public');

                Log::info('📎 Nouveau document uploadé', [
                    'fichier' => $filename,
                    'chemin' => $documentPath,
                    'taille' => $file->getSize()
                ]);
            }

            // ✅ MISE À JOUR DE LA DEMANDE
            $demande->update([
                'date_debut' => $validated['date_debut'],
                'date_fin' => $validated['date_fin'],
                'nb_jours' => $nbJours,
                'motif' => $validated['motif'] ?? $demande->motif,
                'document_justificatif' => $documentPath,
                'statut' => 'En attente',
                'validateur_id' => null,
                'date_validation' => null,
                'motif_refus' => null
            ]);

            Log::info('✅ Demande modifiée', [
                'demande_id' => $demande->id_demande,
                'employe' => $user->email,
                'nb_jours' => $nbJours,
                'document' => $documentPath ? 'Oui' : 'Non'
            ]);

            // Notifier le chef
            $this->envoyerNotificationChef($demande, $user);

            return response()->json([
                'success' => true,
                'message' => '✅ Votre demande a été modifiée et renvoyée pour approbation. Un email a été envoyé à votre chef.'
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => '❌ Erreur de validation',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Erreur modification demande: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => '❌ Une erreur est survenue lors de la modification.'
            ], 500);
        }
    }

    /**
     * Signaler un retour anticipé
     */
    public function retourAnticipe(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $demande = DemandeConge::where('id_demande', $id)
                ->where('user_id', $user->id_user)
                ->where('statut', 'Approuvé')
                ->firstOrFail();

            $validated = $request->validate([
                'nouvelle_date_fin' => 'required|date|before:' . $demande->date_fin . '|after_or_equal:' . $demande->date_debut
            ]);

            // Recalculer le nombre de jours réellement pris (TOUS LES JOURS CALENDAIRES)
            $nouveauxJours = $this->calculerNombreJoursCalendaires($demande->date_debut, $validated['nouvelle_date_fin']);

            $demande->update([
                'date_fin' => $validated['nouvelle_date_fin'],
                'nb_jours' => $nouveauxJours,
                'retour_anticipe' => true
            ]);

            // Réactiver le compte immédiatement
            $user->update(['actif' => 1]);

            // Notifier le chef du retour anticipé
            $departement = Departement::find($user->departement_id);
            if ($departement && $departement->chef_departement_id) {
                $chef = User::find($departement->chef_departement_id);
                if ($chef && $chef->email) {
                    Log::info('Retour anticipé signalé', [
                        'employe' => $user->email,
                        'chef' => $chef->email,
                        'nouvelle_date_fin' => $validated['nouvelle_date_fin']
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => '✅ Retour anticipé enregistré. Votre compte a été réactivé et votre chef a été notifié.'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur retour anticipé: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => '❌ Une erreur est survenue.'
            ], 500);
        }
    }

    /**
     * ✅ NOUVEAU : Calculer le nombre de jours CALENDAIRES (tous les jours inclus)
     * Du 20 au 26 = 7 jours (weekends et fériés inclus)
     */
    private function calculerNombreJoursCalendaires($dateDebut, $dateFin)
    {
        $debut = Carbon::parse($dateDebut);
        $fin = Carbon::parse($dateFin);

        // +1 car on compte les deux dates inclusivement
        return $debut->diffInDays($fin) + 1;
    }

    /**
     * ✅ MODIFIÉ : Calculer le nombre de jours OUVRÉS (pour le calcul du solde uniquement)
     * Exclut UNIQUEMENT les weekends (pas les jours fériés sauf si férié = weekend)
     */
    private function calculerNombreJoursOuvres($dateDebut, $dateFin)
    {
        $debut = Carbon::parse($dateDebut);
        $fin = Carbon::parse($dateFin);
        $jours = 0;

        // Parcourir chaque jour de la période
        while ($debut->lte($fin)) {
            // Compter uniquement les jours qui NE SONT PAS des weekends
            if (!$debut->isWeekend()) {
                $jours++;
            }
            $debut->addDay();
        }

        return $jours;
    }

    /**
     * Récupérer les jours fériés du Gabon (année en cours + 3 ans)
     * NOTE : Cette fonction n'est plus utilisée pour le calcul du nombre de jours
     * mais conservée pour référence future
     */
    private function getJoursFeriesGabon()
    {
        $anneeActuelle = Carbon::now()->year;
        $joursFeries = [];

        for ($annee = $anneeActuelle; $annee <= $anneeActuelle + 3; $annee++) {
            $joursFeries = array_merge($joursFeries, [
                "{$annee}-01-01", // Nouvel An
                "{$annee}-04-17", // Fête nationale
                "{$annee}-05-01", // Fête du Travail
                "{$annee}-08-17", // Fête de l'Indépendance
                "{$annee}-11-01", // Toussaint
                "{$annee}-12-25", // Noël
            ]);
        }

        return $joursFeries;
    }

    /**
     * ✅ MODIFIÉ : Calculer le solde de congés disponible
     * Règle : 1 mois de travail = 2 jours de congé
     * Le calcul du solde utilise uniquement les jours OUVRÉS (sans weekends)
     * Mais les jours fériés sont inclus dans le solde disponible
     */
    private function calculerSoldeDisponible($user)
    {
        // Calculer depuis la date de création du compte (created_at)
        $dateCreation = Carbon::parse($user->created_at);
        $aujourdhui = Carbon::now();

        // Calculer les mois depuis la création du compte (uniquement quand actif)
        $moisTravailles = $dateCreation->diffInMonths($aujourdhui);
        $soldeAccumule = $moisTravailles * 2;

        // Si l'employé n'a pas encore travaillé 1 mois complet, le solde est 0
        if ($soldeAccumule < 2) {
            return 0;
        }

        // Soustraire les congés "Autre" et "Congés payés" déjà APPROUVÉS
        // On utilise le nb_jours tel qu'enregistré (jours calendaires)
        $congesPris = DemandeConge::where('user_id', $user->id_user)
            ->where('statut', 'Approuvé')
            ->whereHas('typeConge', function ($query) {
                $query->whereIn(DB::raw('LOWER(nom_type)'), ['congé payé', 'congés payés', 'autre']);
            })
            ->sum('nb_jours');

        // Le solde ne peut pas être négatif
        return max(0, $soldeAccumule - $congesPris);
    }

    /**
     * Vérifier le quota de congés simultanés dans le département (30% maximum)
     */
    private function verifierQuotaDepartement($departementId, $dateDebut, $dateFin, $demandeIdExclure = null)
    {
        // Nombre total d'employés dans le département
        $totalEmployes = User::where('departement_id', $departementId)->count();

        if ($totalEmployes == 0) {
            return true; // Pas de quota si pas d'employés
        }

        // Quota max : 30% de l'effectif
        $quotaMax = ceil($totalEmployes * 0.30);

        // Compter les employés déjà en congé APPROUVÉ sur cette période
        $query = DemandeConge::whereHas('user', function ($q) use ($departementId) {
                $q->where('departement_id', $departementId);
            })
            ->where('statut', 'Approuvé')
            ->where(function ($query) use ($dateDebut, $dateFin) {
                $query->whereBetween('date_debut', [$dateDebut, $dateFin])
                    ->orWhereBetween('date_fin', [$dateDebut, $dateFin])
                    ->orWhere(function ($q) use ($dateDebut, $dateFin) {
                        $q->where('date_debut', '<=', $dateDebut)
                          ->where('date_fin', '>=', $dateFin);
                    });
            });

        // Exclure la demande en cours de modification
        if ($demandeIdExclure) {
            $query->where('id_demande', '!=', $demandeIdExclure);
        }

        $employesEnConge = $query->count();

        return $employesEnConge < $quotaMax;
    }

    /**
     * Envoyer notification par email au chef de département
     */
    private function envoyerNotificationChef($demande, $employe)
    {
        try {
            // Récupérer le chef de département
            $departement = Departement::find($employe->departement_id);

            if ($departement && $departement->chef_departement_id) {
                $chef = User::find($departement->chef_departement_id);

                if ($chef && $chef->email) {
                    $emailEnvoye = $this->mailService->envoyerNouvelleDemande($demande, $employe, $chef);

                    if ($emailEnvoye) {
                        Log::info('✅ Email de notification envoyé au chef', [
                            'chef' => $chef->email,
                            'employe' => $employe->email,
                            'demande_id' => $demande->id_demande
                        ]);
                        return true;
                    } else {
                        Log::warning('⚠️ Échec envoi email au chef', [
                            'chef' => $chef->email,
                            'employe' => $employe->email
                        ]);
                        return false;
                    }
                }
            }
            return false;
        } catch (\Exception $e) {
            Log::error('❌ Erreur envoi email chef: ' . $e->getMessage());
            return false;
        }
    }
}
