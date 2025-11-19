<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifications extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id_notification';
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'titre',
        'message',
        'type_notification',
        'lu',
        'url_action',
        'document_info'
    ];

    protected $casts = [
        'lu' => 'boolean',
        'created_at' => 'datetime',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }
}
