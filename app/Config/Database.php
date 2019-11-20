<?php

namespace App\Config;
use Illuminate\Database\Capsule\Manager as DB;

class Database {

    public static function connect(){
        $bddConfig = parse_ini_file('config.ini');

        $db = new DB();
        $db->addConnection( [
            'driver'    => $bddConfig['driver'],
            'host'      => $bddConfig['host'],
            'database'  => $bddConfig['database'],
            'username'  => $bddConfig['username'],
            'password'  => $bddConfig['password'],
            'charset'   => $bddConfig['charset'],
            'collation' => $bddConfig['collation'],
            'prefix'    => ''
        ]);
        $db->setAsGlobal();
        $db->bootEloquent();
    }
}