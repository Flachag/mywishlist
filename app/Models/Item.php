<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Item
 * @package App\Models
 */
class Item extends Model {
    /**
     * @var string - Attribut pour lier a la table item
     */
    protected $table = "item";

    /**
     * @var string - Attribut pour représenter la clé primaire
     */
    protected $primaryKey = "id";

    /**
     * @var bool - Attribut pour représenter le timestamps
     */
    public $timestamps = false;

    /**
     * @var array - Attribut qui associe la réservation au nom
     */
    protected $appends = ['book', 'name'];

    /**
     * Méthode d'association qui donne la liste ou l'objet est
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function liste() {
        return $this->belongsTo('App\Models\Liste', 'no');
    }

    /**
     * Méthode d'association qui donne la réservation de l'objet
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function reservation() {
        $res = $this->hasOne('App\Models\Reservation', 'item_id');

        $this->attributes['book'] = !$res->get()->isEmpty();
        if ($this->attributes['book']){
            $this->attributes['name'] = $res->get()->first()->nom;
        }
        return $res;
    }

    /**
     * Méthode qui donne les attributs de réservation
     * @return mixed
     */
    public function getBookAttribute()
    {
        $this->reservation();
        return $this->attributes['book'];
    }

    /**
     * Méthode qui donne le nom de la réservation
     * @return mixed
     */
    public function getNameAttribute()
    {
        $this->reservation();
        return $this->attributes['name'];
    }
}