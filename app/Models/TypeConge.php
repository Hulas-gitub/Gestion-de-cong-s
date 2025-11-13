<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TypeConge extends Model
{
    protected $table = 'types_conges';
    protected $primaryKey = 'id_type';
    public $timestamps = false;

    protected $fillable = [
        'nom_type',
        'couleur_calendrier',
        'duree_max_jours',
        'necessite_justificatif',
        'actif'
    ];

    public function demandes()
    {
        return $this->hasMany(DemandeConge::class, 'type_conge_id', 'id_type');
    }
}
