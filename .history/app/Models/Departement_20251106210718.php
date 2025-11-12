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

    public function users()
    {
        return $this->hasMany(User::class, 'departement_id', 'id_departement');
    }

    public function chef()
    {
        return $this->belongsTo(User::class, 'chef_departement_id', 'id_user');
    }
}
