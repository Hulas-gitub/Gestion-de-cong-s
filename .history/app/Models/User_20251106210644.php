<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user';

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

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'date_embauche' => 'date',
        'actif' => 'boolean',
    ];

    /**
     * Relation avec le rôle
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id_role');
    }

    /**
     * Relation avec le département
     */
    public function departement()
    {
        return $this->belongsTo(Departement::class, 'departement_id', 'id_departement');
    }

    /**
     * Demandes de congés de l'utilisateur
     */
    public function demandes()
    {
        return $this->hasMany(DemandeConge::class, 'user_id', 'id_user');
    }

    /**
     * Notifications de l'utilisateur
     */
    public function notifications()
    {
        return $this->hasMany(Notifications::class, 'user_id', 'id_user');
    }
}
