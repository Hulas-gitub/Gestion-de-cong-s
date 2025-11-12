<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departement extends Model
{
    protected $table = 'departements';
    protected $primaryKey = 'id_departement';
    public $timestamps = false;

    protected $fillable = [
        'nom_departement',
        'description',
        'chef_departement_id',
        'couleur_calendrier',
        'actif'
    ];

    protected $casts = [
        'actif' => 'boolean'
    ];

    // Relation avec le chef de département
    public function chefDepartement()
    {
        return $this->belongsTo(User::class, 'chef_departement_id', 'id_user');
    }

    // Relation avec les employés
    public function employes()
    {
        return $this->hasMany(User::class, 'departement_id', 'id_departement');
    }
}
