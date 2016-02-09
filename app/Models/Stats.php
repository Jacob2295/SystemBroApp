<?php
namespace App\Models;

class Stats {

    public function __construct()
    {
        $this->mongoCollection = ( new \MongoClient() )->selectDB( 'SystemBro' )->selectCollection('stats');
    }

    private function returnMostRecentRecord()
    {
        return $this->findOne([],['createdAt'=>-1]);
    }

    private function findOne( array $query, array $sort = [])
    {
        return iterator_to_array($this->mongoCollection->find($query)->limit(1)->sort($sort));
    }

    public function insert( array $stats )
    {
        $stats['createdAt'] = time();
        $this->mongoCollection->insert($stats);
    }

    public function retrieveTransferAndDisk()
    {
        $latestRecord = $this->returnMostRecentRecord();
        $oneWeekAgo = $this->findOne( [ 'timeCreated'=> ['$gte' => strtotime('-1 week') ] ], [ 'timeCreated' => 1 ] );
        $oneMonthAgo = $this->findOne( [ 'timeCreated'=> ['$gte' => strtotime('-1 month') ] ], [ 'timeCreated' => 1 ] );
        $oneDayAgo = $this->findOne( [ 'timeCreated'=> ['$gte' => strtotime('-1 day') ] ], [ 'timeCreated' => 1 ] );

        return [
            'monthlyTransfer' => $latestRecord['transferOut'] - $oneMonthAgo['transferOut'],
            'weeklyTransfer'  => $latestRecord['transferOut'] - $oneWeekAgo['transferOut'],
            'dailyTransfer'   => $latestRecord['transferOut'] - $oneDayAgo['transferOut']
        ];
    }


}