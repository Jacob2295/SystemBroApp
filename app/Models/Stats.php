<?php
namespace App\Models;

/**
 * Class Stats
 *
 * @package App\Models
 */
use App\GlobalHelpers;

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
                    "_id"       => '$fromServer.hostname',
                    'memFree'   => ['$first' => '$memory.free'],
                    'memTotal'  => ['$first' => '$memory.total'],
                    'diskFree'  => ['$first' => '$disk.free'],
                    'diskTotal' => ['$first' => '$disk.total'],
                    'cpu1min'   => ['$first' => '$cpu.1minAverage'],
                    'ip'        => ['$first' => '$fromServer.ip'],
                    'activeSsh' => ['$first' => '$activeSsh'],
                    'uptime'    => ['$first' => '$uptime'],
                    'createdAt' => ['$first' => '$createdAt'],
                ],
            ]
        ])['result'];
    }

    /**
     * @param $aggregates
     * @return mixed
     */
    public function formatRecent($aggregates)
    {
        foreach ($aggregates as &$aggregate) {
            $bandwidth = $this->retrieveTransfer($aggregate);

            $aggregate['formatted'] = GlobalHelpers::arrFormatBytes(
                [
                    'memFree'   => (int)$aggregate['memFree'],
                    'memTotal'  => (int)$aggregate['memTotal'],
                    'diskFree'  => (int)$aggregate['diskFree'],
                    'diskTotal' => (int)$aggregate['diskTotal'],
                ]
            );

            $aggregate['uptime'] = GlobalHelpers::secondsToTime($aggregate['uptime']);

            $aggregate['formatted']['memPercent'] = ceil((($aggregate['memTotal'] - $aggregate['memFree']) / $aggregate['memTotal']) * 100) . '%';

            $aggregate['formatted']['diskPercent'] = ceil((($aggregate['diskTotal'] - $aggregate['diskFree']) / $aggregate['diskTotal']) * 100) . '%';

            $aggregate['formatted']['bandwidth'] = $bandwidth;

            $aggregate['historicalRecords'] = $this->historicalMemAndCpu($aggregate);

            unset($aggregate['memFree'], $aggregate['memTotal'],
                $aggregate['diskFree'], $aggregate['diskTotal']);

        }

        return $aggregates;
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
    public function retrieveTransfer($individualServerRecord)
    {

        $bandwidthConsumed = [];

        foreach (['month' => '-1 month', 'week' => '-1 week', 'day' => '-1 day'] as $key => $timeSpan) {
            $bandwidthConsumed[$key] = GlobalHelpers::formatBytes(GlobalHelpers::local_min($this->getRecordsFromNTillNow($timeSpan, $individualServerRecord['_id'], ['bandwidth.out', 'bandwidth.in'])));

        }

        return $bandwidthConsumed;
    }

    /**
     * @return array
     */
    public function grabStats()
    {
        return [
            'mostRecent' => $this->formatRecent($this->returnMostRecentRecords()),
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


    /**
     * @param       $daysAgo
     * @param       $hostname
     * @param array $projection
     * @return array
     */
    public function getRecordsFromNTillNow($daysAgo, $hostname, array $projection)
    {
        $items = iterator_to_array($this->mongoCollection->find([
            'createdAt'           => ['$gte' => strtotime($daysAgo)],
            'fromServer.hostname' => $hostname
        ], $projection));

        foreach ($items as &$item) {
            unset($item['_id']);

            $in = 0;
            $out = 0;

            array_walk_recursive($item, function ($leaf, $node) use (&$item, &$in, &$out) {
                if ($node == 'in') {
                    $in = $leaf;
                }

                if ($node == 'out') {
                    $out = $leaf;
                }

            });
            $item = (int)$in + (int)$out;
        }

        return array_values($items);

    }


    public function historicalMemAndCpu($aggregate)
    {
        $items = iterator_to_array($this->mongoCollection->find([
            'fromServer.hostname' => $aggregate['_id']
        ], [
            'memory.free', 'memory.total', 'cpu.1minAverage'
        ])->sort(
            [
                'createdAt' => -1
            ]
        )->limit(10));

    }


}