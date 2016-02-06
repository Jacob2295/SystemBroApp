<?php
namespace App\Models;

class Stats {

    public function __construct()
    {
        $this->mongoCollection = ( new \MongoClient() )->selectDB( 'SystemBro' )->selectCollection('stats');
    }


}