<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Message
 * @package App\Models
 */
class Message extends Model {
    /**
     * @var string - Attribut pour lier a la table message
     */
    protected $table = "message";

    /**
     * @var string - Attribut pour représenter la clé primaire
     */
    protected $primaryKey = "id";

    /**
     * @var bool - Attribut pour représenter le timestamps
     */
    public $timestamps = false;
}