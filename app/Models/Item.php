<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model {
    protected $table = "item";
    protected $primaryKey = "id";
    public $timestamps = false;
    protected $appends = ['book'];

    public function liste() {
        return $this->belongsTo('App\Models\Liste', 'no');
    }

    public function reservation() {
        $res = $this->hasOne('App\Models\Reservation', 'item_id');
        $this->attributes['book'] = !$res->get()->isEmpty();
        return $res;
    }
    public function getBookAttribute()
    {
        $this->reservation();
        return $this->attributes['book'];
    }
}