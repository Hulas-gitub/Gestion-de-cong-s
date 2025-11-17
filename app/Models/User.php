<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // IMPORTANT : Spécifier la clé primaire personnalisée
    protected $primaryKey = 'id_user';

    // IMPORTANT : Spécifier le nom de la table
    protected $table = 'users';

    // IMPORTANT : Indiquer que la clé primaire est auto-incrémentée
    public $incrementing = true;

    // Type de la clé primaire
    protected $keyType = 'int';

    /**
     * Les attributs qui sont assignables en masse.
     */
    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'telephone',
        'profession',
        'photo_url',
        'matricule',
        'date_embauche',
        'role_id',
        'departement_id',
        'solde_conges_annuel',
        'conges_pris',
        'actif',
    ];

    /**
     * Les attributs qui doivent être cachés pour la sérialisation.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Les attributs qui doivent être castés.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_embauche' => 'date',
        'actif' => 'boolean',
        'solde_conges_annuel' => 'integer',
        'conges_pris' => 'integer',
        'password' => 'hashed', // Laravel 10+
    ];

    /**
     * Relations
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id_role');
    }

    public function departement()
    {
        return $this->belongsTo(Departement::class, 'departement_id', 'id_departement');
    }

    public function demandesConges()
    {
        return $this->hasMany(DemandeConge::class, 'user_id', 'id_user');
    }

    public function notifications()
    {
        return $this->hasMany(Notifications::class, 'user_id', 'id_user');
    }

    /**
     * Obtenir le nom complet de l'utilisateur
     */
    public function getFullNameAttribute()
    {
        return "{$this->prenom} {$this->nom}";
    }

    /**
     * Obtenir les congés restants
     */
    public function getCongesRestantsAttribute()
    {
        return $this->solde_conges_annuel - $this->conges_pris;
    }

    /**
     * Vérifier si l'utilisateur est admin
     */
    public function isAdmin()
    {
        return $this->role && $this->role->nom_role === 'Admin';
    }

    /**
     * Vérifier si l'utilisateur est chef de département
     */
    public function isChefDepartement()
    {
        return $this->role && $this->role->nom_role === 'chef de departement';
    }

    /**
     * Vérifier si l'utilisateur est employé
     */
    public function isEmploye()
    {
        return $this->role && $this->role->nom_role === 'emplpoyé';
    }
}
