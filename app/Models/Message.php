<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model {
    protected $table = "message";
    protected $primaryKey = "id";
    public $timestamps = false;
}