<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Utilisateur extends Model{
    protected $table = 'utilisateur';
    protected $primaryKey = 'id' ;
    public $timestamps = false;

    public function reservation(){
        return $this->hasMany('Models\Reservation', 'id');
    }
}