<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

/**
 * Class Utilisateur
 * @package App\Models
 */
class Utilisateur extends Model{
    /**
     * @var string - Attribut pour lier a la table utilisateur
     */
    protected $table = 'utilisateur';

    /**
     * @var string - Attribut pour représenter la clé primaire
     */
    protected $primaryKey = 'id';

    /**
     * @var bool - Attribut pour représenter le timestamps
     */
    public $timestamps = false;

    /**
     * Méthode d'association qui donne les réservations d'un utilisateur
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function reservation(){
        return $this->hasMany('Models\Reservation', 'id');
    }

    /**
     * Méthode d'association qui donne les listes d'un utilisateur
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function listes(){
        return $this->hasMany('Models\Liste', 'user_id');
    }
}