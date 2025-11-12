<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DemandeConge extends Model
{
    protected $table = 'demandes_conges';
    protected $primaryKey = 'id_demande';

    protected $fillable = [
        'user_id',
        'type_conge_id',
        'date_debut',
        'date_fin',
        'nb_jours',
        'motif',
        'statut',
        'commentaire_refus',
        'validateur_id',
        'date_validation',
        'document_justificatif'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'date_validation' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function validateur()
    {
        return $this->belongsTo(User::class, 'validateur_id', 'id_user');
    }
}
