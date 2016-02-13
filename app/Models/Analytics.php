<?php
namespace App\Models;

use Carbon\Carbon;
use GeoIp2\Database\Reader;

/**
 * Class LoggingModel
 *
 * @package App\Models
 */
class Analytics
{

    /**
     * Needs DI, but for time being, retrieves
     * mongo Cursor
     *
     * LoggingModel constructor.
     */
    public function __construct()
    {
        $this->mongoCollectionForAccess = (new \MongoClient())->selectDB('SystemBro')->selectCollection('accessLogs');
    }

    /**
     * Inserts the access log information in
     * the database
     *
     * @param array $alogs Access Log lines
     * @param       $fromServer array The sender of these access logs
     *
     * @throws \Kassner\LogParser\FormatException
     */
    public function insertAccessLogging(array $alogs, $fromServer)
    {

        if (!(count($alogs) > 0)) {
            return;
        }

        $parser = new \Kassner\LogParser\LogParser();
        $parser->setFormat('%h %l %u %t "%r" %>s %O "%{Referer}i" \"%{User-Agent}i"');
        $toBeInserted = [];
        foreach ($alogs as $line) {
            $userSpec = $parser->parse($line);

            if ($userSpec->host === '::1') {
                continue;
            }

            $userSpec->device = parse_user_agent($userSpec->HeaderUserAgent);

            $city = new Reader(database_path() . '/GeoLite2-City.mmdb');

            $geoRecord = $city->city($userSpec->host);

            $userSpec->fromServer = $fromServer;

            $userSpec->location = [
                'city'    => $geoRecord->city->name,
                'country' => $geoRecord->country->name,
            ];
            $userSpec->createdAt = time();
            $toBeInserted[] = $userSpec;
        }

        $this->mongoCollectionForAccess->batchInsert($toBeInserted);
    }

    /**
     * Obtains the unique visitors of a given server
     *
     * @param $aggregate
     * @return array Integer counts of given timespan
     */
    public function getUniqueVisits($aggregate)
    {
        return [
            'day'   => count($this->mongoCollectionForAccess->distinct('host', ['fromServer.hostname' => $aggregate['_id'], 'stamp' => ['$gt' => time() - 86400]])),
            'week'  => count($this->mongoCollectionForAccess->distinct('host', ['fromServer.hostname' => $aggregate['_id'], 'stamp' => ['$gt' => time() - 86400 * 7]])),
            'month' => count($this->mongoCollectionForAccess->distinct('host', ['fromServer.hostname' => $aggregate['_id'], 'stamp' => ['$gt' => time() - 86400 * 30]])),
        ];
    }

    /**
     * Obtains the total resource requests of a given server
     *
     * @param $aggregate
     * @return array Integer counts of given timespan
     */
    public function getTotalRequestCount($aggregate)
    {
        return [
            'day'   => $this->mongoCollectionForAccess->count(['fromServer.hostname' => $aggregate['_id'], 'stamp' => ['$gt' => time() - 86400]]),
            'week'  => $this->mongoCollectionForAccess->count(['fromServer.hostname' => $aggregate['_id'], 'stamp' => ['$gt' => time() - 86400 * 7]]),
            'month' => $this->mongoCollectionForAccess->count(['fromServer.hostname' => $aggregate['_id'], 'stamp' => ['$gt' => time() - 86400 * 30]]),
        ];
    }

    /**
     * Retrieves the most recent visitors to the
     * site, limited at 20
     *
     * @return mixed
     */
    public function getRecentVisitors()
    {
        $visitors = $this->mongoCollectionForAccess->aggregate([
            '$group' => [
                '_id'           => '$host',
                'time'          => ['$last' => '$stamp'],
                'requestedPage' => ['$last' => '$request'],
                'device'        => ['$last' => '$device'],
                'location'      => ['$last' => '$location'],
                'status'        => ['$last' => '$status'],
                'count'         => ['$sum' => 1],
            ]], ['$sort' => ['time' => -1]], ['$limit' => 20]
        )['result'];

        foreach ($visitors as &$visitor) {
            $visitor['time'] = Carbon::createFromTimestamp($visitor['time'])->diffForHumans();
        }

        return $visitors;

    }

    /**
     * Counts the HTTP response codes
     *
     * @return array Counts of each HTTP response code
     */
    public function getHttpCodeCount()
    {
        return $this->mongoCollectionForAccess->aggregate([
            '$group' => [
                '_id'   => '$status',
                'count' => ['$sum' => 1]
            ]
        ])['result'];
    }

    /**
     * Collects everything and puts it all together
     *
     * @param $aggregate
     * @return array Aggregation information
     */
    public function aggregateAnalytics($aggregate)
    {
        return [
            'uniqueVisits'          => $this->getUniqueVisits($aggregate),
            'totalRequestCount'     => $this->getTotalRequestCount($aggregate),
            'recentVisitors'        => $this->getRecentVisitors(),
            'HttpResponseCodeCount' => $this->getHttpCodeCount(),
        ];
    }

}
