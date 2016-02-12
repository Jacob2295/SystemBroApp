<?php
namespace App\Models;

/**
 * Class Stats
 *
 * @package App\Models
 */
use App\GlobalHelpers;
use App\Helpers;

/**
 * Class Stats
 *
 * @package App\Models
 */
class Stats
{

    /**
     * Initialize mongoClient
     */
    public function __construct()
    {
        $this->mongoCollection = (new \MongoClient())->selectDB('SystemBro')->selectCollection('stats');
    }

    /**
     * @return array
     */
    private function returnMostRecentRecords()
    {
        return $this->mongoCollection->aggregate([
            [
                '$match' => [
                    "fromServer" => ['$ne' => NULL]
                ]
            ],
            [
                '$sort' => [
                    "createdAt" => -1
                ]
            ],
            [
                '$group' => [
                    "_id"          => '$fromServer.hostname',
                    'memFree'      => ['$first' => '$memory.free'],
                    'memTotal'     => ['$first' => '$memory.total'],
                    'bandwidthIn'  => ['$first' => '$bandwidth.in'],
                    'bandwidthOut' => ['$first' => '$bandwidth.out'],
                    'diskFree'     => ['$first' => '$disk.free'],
                    'diskTotal'    => ['$first' => '$disk.total'],
                    'cpu1min'      => ['$first' => '$cpu.1minAverage'],
                    'ip'           => ['$first' => '$fromServer.ip'],
                    'activeSsh'    => ['$first' => '$activeSsh'],
                    'uptime'       => ['$first' => '$uptime'],
                    'createdAt'    => ['$first' => '$createdAt'],
                ],
            ]
        ])['result'];
    }

    /**
     * @param array $query
     * @param array $sort
     *
     * @return array
     */
    private function findOne(array $query, array $sort = [])
    {
        return iterator_to_array($this->mongoCollection->find($query)->limit(1)->sort($sort));
    }

    /**
     * @param array $stats
     */
    public function insert(array $stats)
    {
        $stats['createdAt'] = time();
        $this->mongoCollection->insert($stats);
    }

    /**
     * @return array
     */
    public function retrieveTransfer()
    {
        foreach ($this->returnMostRecentRecords() as $individualServerRecord) {

            $previousRecords = $this->getRecordFromNDaysAgo(['-1 day', '-1 month', '-1 week'], $individualServerRecord['_id']);

            dd(GlobalHelpers::local_min($this->getRecordsFromNTillNow('-1 month', $individualServerRecord['_id'], ['bandwidth.out' => 1])));

            foreach ($previousRecords as $timeSpan => $previousRecord) {
                $bandwidthTotal[$timeSpan] =
                    ($individualServerRecord['bandwidthOut'] - $previousRecord['bandwidth']['out']) +
                    ($individualServerRecord['bandwidthIn'] - $previousRecord['bandwidth']['in']);
            }
        }


        return $bandwidthTotal;
    }

    /**
     * @return array
     */
    public function grabStats()
    {
        return [
            'mostRecent' => $this->retrieveTransfer()
        ];
    }

    /**
     * @param array $daysAgo
     * @param       $hostname
     *
     * @return mixed
     */
    public function getRecordFromNDaysAgo(array $daysAgo, $hostname)
    {
        $records = [];

        foreach ($daysAgo as $timeSpan) {
            $records[$timeSpan] = $this->mongoCollection->findOne([
                'createdAt'           => ['$gte' => strtotime($timeSpan)],
                'fromServer.hostname' => $hostname
            ]);
        }

        return $records;
    }


    public function getRecordsFromNTillNow($daysAgo, $hostname, array $projection)
    {
        $items = iterator_to_array($this->mongoCollection->find([
            'createdAt'           => ['$gte' => strtotime($daysAgo)],
            'fromServer.hostname' => $hostname
        ], $projection));

        foreach ($items as &$item) {
            unset($item['_id']);

            array_walk_recursive($item, function ($leaf) use (&$item) {
                $item = (int)$leaf;
            });
        }

        return array_values($items);

    }


}