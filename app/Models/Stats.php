<?php
namespace App\Models;

/**
 * Class Stats
 *
 * @package App\Models
 */
use App\GlobalHelpers;
use App\Models\Analytics;
use Carbon\Carbon;

/**
 * Class Stats
 *
 * @package App\Models
 */
class Stats
{

    /**
     * Gives us the mongoCursor
     *
     * Initialize mongoClient
     */
    public function __construct()
    {
        $this->mongoCollection = (new \MongoClient())->selectDB('SystemBro')->selectCollection('stats');
    }

    /**
     * Aggregate pipeline to collect a flattened array
     * of the most recent servers. Intended for DynamoDB
     * hence the need for a flattening
     *
     * @return array A flat array of recent records
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
     * TODO: Needs serious refactor
     * Collects the whole data package for
     * the most recent aggregates
     *
     * @param $aggregates
     * @return mixed
     */
    public function formatRecent($aggregates)
    {
        $aggregates['allowedServers'] = (new AllowedServers())->getServers();
        foreach ($aggregates['servers'] as &$aggregate) {

            $aggregate['analytics'] = (new Analytics())->aggregateAnalytics($aggregate);

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

            $aggregate['cpu1min'] = $aggregate['cpu1min'] . '%';

            $aggregate['formatted']['bandwidth'] = $bandwidth;

            $aggregate['createdAt'] = Carbon::createFromTimestamp($aggregate['createdAt'])->diffForHumans();
            $aggregate['historicalRecords'] = $this->historicalMemAndCpu($aggregate);

            unset($aggregate['memFree'], $aggregate['memTotal'],
                $aggregate['diskFree'], $aggregate['diskTotal']);

        }

        return $aggregates;
    }

    /**
     * Inserts records into the
     * stats collection, and append
     * a createdAt timestamp
     *
     * @param array $stats
     */
    public function insert(array $stats)
    {
        $stats['createdAt'] = time();
        $this->mongoCollection->insert($stats);
    }

    /**
     * Denotes the amount of bandwidth consumed
     * for different intervals
     *
     * @return array Bandwidth consumed
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
     * Grabs the basic server stats
     *
     * @return array Basic server statistics
     */
    public function grabStats()
    {
        return $this->formatRecent(['servers' => $this->returnMostRecentRecords()]);
    }

    /**
     * Returns a single records from $daysago
     *
     * @see getRecordsFromNTillNow
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
     * Collects records spanning an interval between
     * now and $daysago
     *
     * @param       $daysAgo string -1 month, -1 day, etc..
     * @param       $hostname array denotes the senders server
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


    /**
     * Collects the previously collected CPU
     * and RAM information for charting
     *
     * @param $aggregate
     * @return array [ CPU => {val}, MEM => {VAL} ]
     */
    public function historicalMemAndCpu($aggregate)
    {
        $stats = iterator_to_array($this->mongoCollection->find([
            'fromServer.hostname' => $aggregate['_id']
        ], [
            'memory.free', 'memory.total', 'cpu.1minAverage', 'createdAt'
        ])->sort(
            [
                'createdAt' => -1
            ]
        )->limit(10));

        foreach ($stats as &$stat) {
            unset($stat['_id']);
            foreach ($stat as $key => &$itemInStat) {
                if ($key == 'memory') {
                    $itemInStat['memPercent'] = ceil((($itemInStat['total'] - $itemInStat['free']) / $itemInStat['total']) * 100);
                    unset($itemInStat['free'],$itemInStat['total']);
                }
            }
            $stat['memory'] = array_pop($stat['memory']);
            $stat['cpu'] = (int)array_pop($stat['cpu']);
        }

        return array_values($stats);
    }


}