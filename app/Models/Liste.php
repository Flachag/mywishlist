<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;

class Liste extends Model {
    protected $table = "liste";
    protected $primaryKey = "no";
    public $timestamps = false;

    public function items() {
        return $this->hasMany('\App\Models\Item', 'liste_id');
    }

    public function haveExpired(): bool {
        return new DateTime() > new DateTime($this->expiration);
    }
}