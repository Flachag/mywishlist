<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Liste
 * @package App\Models
 */
class Liste extends Model {
    /**
     * @var string - Attribut pour lier a la table liste
     */
    protected $table = "liste";

    /**
     * @var string - Attribut pour représenter la clé primaire
     */
    protected $primaryKey = "no";

    /**
     * @var bool - Attribut pour représenter le timestamps
     */
    public $timestamps = false;

    /**
     * Méthode d'association qui donne les objets d'une liste
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items() {
        return $this->hasMany('\App\Models\Item', 'liste_id');
    }

    /**
     * Méthode qui donne si une liste est expiré
     * @return bool
     * @throws \Exception
     */
    public function haveExpired(): bool {
        return new DateTime() > new DateTime($this->expiration);
    }

    /**
     * Méthode d'association qui donne les messages d'une liste
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages() {
        return $this->hasMany('\App\Models\Message', 'liste_id');
    }
}