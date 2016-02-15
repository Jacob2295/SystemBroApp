<?php
namespace App\Models;


class AllowedServers
{

    public function __construct()
    {
        $this->mongoCollection = (new \MongoClient())->selectDB('SystemBro');
    }

    public function getServers()
    {
        return $this->mongoCollection->selectCollection('allowedServers')->findOne(['type' => 'allowedServerList'])['allowedServers'];
    }

}