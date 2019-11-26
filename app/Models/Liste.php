<?php
namespace App\models;

use Illuminate\Database\Eloquent\Model;

class Liste extends Model{
    protected $table = 'liste';
    protected $primaryKey = 'id' ;
    public $timestamps = false;
    protected $appends = ['items'];

    public function item(){
        return $this->hasMany('App\Models\Item', 'liste_id');
    }

    public function getItemsAttribute()
    {
        return $this->attributes['items'] = Item::where('liste_id', $this->no)->get();
    }
}