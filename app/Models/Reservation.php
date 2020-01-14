<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Reservation
 * @package App\Models
 */
class Reservation extends Model {
    /**
     * @var string - Attribut pour lier a la table réservation
     */
    protected $table = "reservation";

    /**
     * @var string - Attribut pour représenter la clé primaire
     */
    protected $primaryKey = "id";

    /**
     * @var bool - Attribut pour représenter le timestamps
     */
    public $timestamps = false;
}