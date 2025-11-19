<?php

namespace App\Http\Controllers;

use App\Models\TypeConge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class TypeCongesController extends Controller
{
    /**
     * Afficher la page de gestion des types de congés
     */
    public function index()
    {
        return view('admin.type-conges');
    }

    /**
     * Récupérer tous les types de congés
     */
    public function getTypesConges()
    {
        try {
            $typesConges = TypeConge::where('actif', 1)
                ->orderBy('nom_type', 'asc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $typesConges
            ]);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des types de congés: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des types de congés'
            ], 500);
        }
    }

    /**
     * Créer un nouveau type de congé
     */
    public function store(Request $request)
    {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                'nom_type' => 'required|string|max:100|unique:types_conges,nom_type',
                'couleur_calendrier' => 'required|string|max:7'
            ], [
                'nom_type.required' => 'Le nom du type de congé est requis',
                'nom_type.unique' => 'Ce type de congé existe déjà',
                'couleur_calendrier.required' => 'La couleur est requise'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            // Créer le type de congé
            $typeConge = TypeConge::create([
                'nom_type' => $request->nom_type,
                'couleur_calendrier' => $request->couleur_calendrier,
                'duree_max_jours' => $request->duree_max_jours ?? null,
                'necessite_justificatif' => $request->necessite_justificatif ?? 0,
                'actif' => 1
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Type de congé ajouté avec succès',
                'data' => $typeConge
            ], 201);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du type de congé: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du type de congé'
            ], 500);
        }
    }

    /**
     * Afficher un type de congé spécifique
     */
    public function show($id)
    {
        try {
            $typeConge = TypeConge::find($id);

            if (!$typeConge) {
                return response()->json([
                    'success' => false,
                    'message' => 'Type de congé non trouvé'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $typeConge
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération du type de congé: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du type de congé'
            ], 500);
        }
    }

    /**
     * Mettre à jour un type de congé
     */
    public function update(Request $request, $id)
    {
        try {
            $typeConge = TypeConge::find($id);

            if (!$typeConge) {
                return response()->json([
                    'success' => false,
                    'message' => 'Type de congé non trouvé'
                ], 404);
            }

            // Validation
            $validator = Validator::make($request->all(), [
                'nom_type' => 'required|string|max:100|unique:types_conges,nom_type,' . $id . ',id_type',
                'couleur_calendrier' => 'required|string|max:7'
            ], [
                'nom_type.required' => 'Le nom du type de congé est requis',
                'nom_type.unique' => 'Ce type de congé existe déjà',
                'couleur_calendrier.required' => 'La couleur est requise'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first()
                ], 422);
            }

            // Mettre à jour
            $typeConge->update([
                'nom_type' => $request->nom_type,
                'couleur_calendrier' => $request->couleur_calendrier,
                'duree_max_jours' => $request->duree_max_jours ?? $typeConge->duree_max_jours,
                'necessite_justificatif' => $request->necessite_justificatif ?? $typeConge->necessite_justificatif
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Type de congé modifié avec succès',
                'data' => $typeConge
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la modification du type de congé: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la modification du type de congé'
            ], 500);
        }
    }

    /**
     * Supprimer (désactiver) un type de congé
     */
    public function destroy($id)
    {
        try {
            $typeConge = TypeConge::find($id);

            if (!$typeConge) {
                return response()->json([
                    'success' => false,
                    'message' => 'Type de congé non trouvé'
                ], 404);
            }

            // Vérifier si le type de congé est utilisé dans des demandes
            $demandesCount = $typeConge->demandes()->count();

            if ($demandesCount > 0) {
                // Désactiver au lieu de supprimer
                $typeConge->update(['actif' => 0]);

                return response()->json([
                    'success' => true,
                    'message' => 'Type de congé désactivé (utilisé dans ' . $demandesCount . ' demande(s))'
                ]);
            } else {
                // Supprimer si non utilisé
                $typeConge->delete();

                return response()->json([
                    'success' => true,
                    'message' => 'Type de congé supprimé avec succès'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Erreur lors de la suppression du type de congé: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression du type de congé'
            ], 500);
        }
    }
}
