<?php
namespace App\Models;

/**
 * Class Stats
 * @package App\Models
 */
class Stats {

    /**
     * Initialize mongoClient
     */
    public function __construct()
    {
        $this->mongoCollection = ( new \MongoClient() )->selectDB( 'SystemBro' )->selectCollection('stats');
    }

    /**
     * @return array
     */
    private function returnMostRecentRecord()
    {
        return $this->findOne([],['createdAt'=>-1]);
    }

    /**
     * @param array $query
     * @param array $sort
     *
     * @return array
     */
    private function findOne( array $query, array $sort = [])
    {
        return iterator_to_array($this->mongoCollection->find($query)->limit(1)->sort($sort));
    }

    /**
     * @param array $stats
     */
    public function insert( array $stats )
    {
        $stats['createdAt'] = time();
        $this->mongoCollection->insert($stats);
    }

    /**
     * @return array
     */
    public function retrieveTransfer()
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

    /**
     * @return array
     */
    public function grabStats()
    {
        return [
            'transfer' => $this->retrieveTransfer()
        ];
    }


}