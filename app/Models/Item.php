<?php
namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model {
    protected $table = 'item';
    protected $primaryKey = 'id' ;
    public $timestamps = false;
    protected $appends = ['reservation'];

    public function liste(){
        return $this->belongsTo('App\Models\Liste', 'no');
    }

    public function getReservationAttribute()
    {
        return $this->attributes['reservation'] = Reservation::where('id_item',$this->id)->count();
    }
}