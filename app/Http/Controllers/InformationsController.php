<?php

namespace App\Http\Controllers;

use App\Models\Notifications;
use App\Models\User;
use App\Services\MailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class InformationsController extends Controller
{
    protected $mailService;

    public function __construct(MailService $mailService)
    {
        $this->mailService = $mailService;
    }

    /**
     * Afficher la page des notes d'information
     */
    public function index()
    {
        return view('chef-de-departement.informations');
    }

    /**
     * Récupérer toutes les notes d'information (API)
     */
    public function getNotes()
    {
        try {
            $user = Auth::user();

            if (!$user || !$user->departement_id) {
                return response()->json([
                    'success' => true,
                    'notes' => []
                ]);
            }

            $notifications = Notifications::where('type_notification', 'info')
                ->whereHas('user', function($query) use ($user) {
                    $query->where('departement_id', $user->departement_id);
                })
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            $formattedNotes = $notifications->map(function($notification) use ($user) {
                $docInfo = $notification->document_info ? json_decode($notification->document_info, true) : null;

                return [
                    'id' => $notification->id_notification,
                    'titre' => $notification->titre,
                    'message' => $notification->message ?? '',
                    'date' => \Carbon\Carbon::parse($notification->created_at)->format('d/m/Y'),
                    'created_at' => $notification->created_at,
                    'user_id' => $notification->user_id,
                    'is_owner' => $notification->user_id == $user->id_user, // ✅ CORRECTION ICI
                    'has_file' => $docInfo !== null,
                    'file_name' => $docInfo ? $docInfo['nom_fichier'] : null,
                    'file_type' => $docInfo ? strtolower($docInfo['type']) : 'note',
                    'file_size' => $docInfo ? ($docInfo['taille'] ?? null) : null,
                ];
            });

            return response()->json([
                'success' => true,
                'notes' => $formattedNotes
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur getNotes: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du chargement des notes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Créer une nouvelle note d'information
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'titre' => 'required|string|max:255',
                'message' => 'nullable|string',
                'document' => 'nullable|file|mimes:pdf,xlsx,xls,png,jpg,jpeg,doc,docx|max:10240'
            ]);

            $user = Auth::user();

            if (!$user || !$user->departement_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous devez être affecté à un département'
                ], 403);
            }

            $documentInfo = null;

            // Traitement du fichier si présent
            if ($request->hasFile('document')) {
                $file = $request->file('document');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('uploads/informations', $fileName, 'public');

                $documentInfo = json_encode([
                    'nom_fichier' => $file->getClientOriginalName(),
                    'chemin' => $filePath,
                    'type' => $file->getClientOriginalExtension(),
                    'taille' => $file->getSize()
                ]);
            }

            // Créer la notification avec la bonne clé
            $notification = Notifications::create([
                'user_id' => $user->id_user, // ✅ CORRECTION ICI
                'titre' => $request->titre,
                'message' => $request->message ?? '',
                'type_notification' => 'info',
                'lu' => false,
                'document_info' => $documentInfo
            ]);

            // Récupérer tous les employés ACTIFS du département (sauf le chef)
            $employes = User::where('departement_id', $user->departement_id)
                ->where('id_user', '!=', $user->id_user) // ✅ CORRECTION ICI
                ->where('actif', 1)
                ->get();

            Log::info('Début envoi emails note information', [
                'notification_id' => $notification->id_notification,
                'departement_id' => $user->departement_id,
                'total_employes' => $employes->count()
            ]);

            // Envoyer l'email à tous les employés
            $emailsSent = 0;
            $emailsFailed = 0;

            foreach ($employes as $employe) {
                try {
                    $result = $this->mailService->envoyerNouvelleNoteInformation(
                        $employe->email,
                        $employe->nom,
                        $employe->prenom,
                        $user->prenom . ' ' . $user->nom,
                        $notification->titre,
                        $notification->message ?? '',
                        $documentInfo !== null,
                        url('/employes/informations')
                    );

                    if ($result) {
                        $emailsSent++;
                        Log::info('Email envoyé avec succès', [
                            'destinataire' => $employe->email,
                            'notification_id' => $notification->id_notification
                        ]);
                    } else {
                        $emailsFailed++;
                        Log::warning('Échec envoi email', [
                            'destinataire' => $employe->email,
                            'notification_id' => $notification->id_notification
                        ]);
                    }
                } catch (\Exception $e) {
                    $emailsFailed++;
                    Log::error('Erreur envoi email', [
                        'destinataire' => $employe->email,
                        'erreur' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            Log::info('Fin envoi emails note information', [
                'notification_id' => $notification->id_notification,
                'emails_sent' => $emailsSent,
                'emails_failed' => $emailsFailed,
                'total_employes' => $employes->count()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Note publiée avec succès',
                'notification' => $notification,
                'emails_info' => [
                    'sent' => $emailsSent,
                    'failed' => $emailsFailed,
                    'total' => $employes->count()
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur store complète', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de la note',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Afficher une note d'information
     */
    public function show($id)
    {
        try {
            $notification = Notifications::findOrFail($id);
            $user = Auth::user();

            // Marquer comme lu (sauf si c'est le créateur)
            if (!$notification->lu && $notification->user_id != $user->id_user) { // ✅ CORRECTION ICI
                $notification->update(['lu' => true]);
            }

            $docInfo = $notification->document_info ? json_decode($notification->document_info, true) : null;

            return response()->json([
                'success' => true,
                'notification' => [
                    'id' => $notification->id_notification,
                    'titre' => $notification->titre,
                    'message' => $notification->message ?? '',
                    'date' => \Carbon\Carbon::parse($notification->created_at)->format('d/m/Y'),
                    'created_at' => $notification->created_at,
                    'user_id' => $notification->user_id,
                    'has_file' => $docInfo !== null,
                    'file_name' => $docInfo ? $docInfo['nom_fichier'] : null,
                    'file_type' => $docInfo ? strtolower($docInfo['type']) : 'note',
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur show: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Note introuvable'
            ], 404);
        }
    }

    /**
     * Mettre à jour une note d'information
     */
    public function update(Request $request, $id)
    {
        try {
            $notification = Notifications::findOrFail($id);
            $user = Auth::user();

            // Vérifier que l'utilisateur est le créateur
            if ($notification->user_id !== $user->id_user) { // ✅ CORRECTION ICI
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé'
                ], 403);
            }

            $request->validate([
                'titre' => 'required|string|max:255',
                'message' => 'nullable|string',
                'document' => 'nullable|file|mimes:pdf,xlsx,xls,png,jpg,jpeg,doc,docx|max:10240'
            ]);

            $documentInfo = $notification->document_info;

            // Traitement du nouveau fichier si présent
            if ($request->hasFile('document')) {
                // Supprimer l'ancien fichier
                if ($documentInfo) {
                    $oldDoc = json_decode($documentInfo, true);
                    if (isset($oldDoc['chemin'])) {
                        Storage::disk('public')->delete($oldDoc['chemin']);
                    }
                }

                $file = $request->file('document');
                $fileName = time() . '_' . $file->getClientOriginalName();
                $filePath = $file->storeAs('uploads/informations', $fileName, 'public');

                $documentInfo = json_encode([
                    'nom_fichier' => $file->getClientOriginalName(),
                    'chemin' => $filePath,
                    'type' => $file->getClientOriginalExtension(),
                    'taille' => $file->getSize()
                ]);
            }

            $notification->update([
                'titre' => $request->titre,
                'message' => $request->message ?? '',
                'document_info' => $documentInfo
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Note mise à jour avec succès',
                'notification' => $notification
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur update: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour'
            ], 500);
        }
    }

    /**
     * Supprimer une note d'information
     */
    public function destroy($id)
    {
        try {
            $notification = Notifications::findOrFail($id);
            $user = Auth::user();

            // Vérifier que l'utilisateur est le créateur
            if ($notification->user_id !== $user->id_user) { // ✅ CORRECTION ICI
                return response()->json([
                    'success' => false,
                    'message' => 'Non autorisé'
                ], 403);
            }

            // Supprimer le fichier associé
            if ($notification->document_info) {
                $docInfo = json_decode($notification->document_info, true);
                if (isset($docInfo['chemin'])) {
                    Storage::disk('public')->delete($docInfo['chemin']);
                }
            }

            $notification->delete();

            return response()->json([
                'success' => true,
                'message' => 'Note supprimée avec succès'
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur destroy: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression'
            ], 500);
        }
    }

    /**
     * Télécharger le document d'une note
     */
    public function download($id)
    {
        try {
            $notification = Notifications::findOrFail($id);

            if (!$notification->document_info) {
                abort(404, 'Aucun document disponible');
            }

            $docInfo = json_decode($notification->document_info, true);

            if (!isset($docInfo['chemin']) || !Storage::disk('public')->exists($docInfo['chemin'])) {
                abort(404, 'Fichier introuvable');
            }

            return Storage::disk('public')->download(
                $docInfo['chemin'],
                $docInfo['nom_fichier']
            );

        } catch (\Exception $e) {
            Log::error('Erreur download: ' . $e->getMessage());
            abort(404, 'Erreur lors du téléchargement');
        }
    }
}
