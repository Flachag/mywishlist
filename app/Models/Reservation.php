<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Reservation extends Model{
    protected $table = 'reservation';
    protected $primaryKey = 'id' ;
    public $timestamps = false;

    public function utilisateur(){
        return $this->belongsTo('Models\Utilisateur', 'id');
    }
}