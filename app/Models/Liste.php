<?php
namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Liste extends Model{
    protected $table = 'liste';
    protected $primaryKey = 'id' ;
    public $timestamps = false;

    public function item(){
        return $this->hasMany('Models\Item', 'liste_id');
    }
}