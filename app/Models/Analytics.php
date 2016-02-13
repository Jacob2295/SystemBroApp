<?php
namespace App\Models;
use Carbon\Carbon;
use GeoIp2\Database\Reader;

/**
 * Class LoggingModel
 * @package App\Models
 */
class Analytics {

    /**
     * LoggingModel constructor.
     */
    public function __construct()
    {
        $this->mongoCollectionForAccess = ( new \MongoClient() )->selectDB( 'SystemBro' )->selectCollection('accessLogs');
    }

    /**
     * @param array $alogs
     * @param       $fromServer
     *
     * @throws \Kassner\LogParser\FormatException
     */
    public function insertAccessLogging( array $alogs, $fromServer )
    {

        if (! ( count($alogs) > 0 )) {
            return;
        }

        $parser = new \Kassner\LogParser\LogParser();
        $parser->setFormat( '%h %l %u %t "%r" %>s %O "%{Referer}i" \"%{User-Agent}i"' );
        $toBeInserted = [];
        foreach ( $alogs as $line ) {
            $userSpec = $parser->parse( $line );

            if ($userSpec->host === '::1') {
                continue;
            }

            $userSpec->device = parse_user_agent( $userSpec->HeaderUserAgent );

            $city = new Reader( database_path() . '/GeoLite2-City.mmdb' );

            $geoRecord = $city->city( $userSpec->host );

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
     * @return array
     */
    public function getUniqueVisits($aggregate)
    {
        return [
            'day' => count( $this->mongoCollectionForAccess->distinct( 'host', [ 'fromServer.hostname' => $aggregate['_id'], 'stamp' => [ '$gt' => time() - 86400 ] ] ) ),
            'week' => count( $this->mongoCollectionForAccess->distinct( 'host', [ 'fromServer.hostname' => $aggregate['_id'], 'stamp' => [ '$gt' => time() - 86400 * 7 ] ] ) ),
            'month' => count( $this->mongoCollectionForAccess->distinct( 'host', [ 'fromServer.hostname' => $aggregate['_id'], 'stamp' => [ '$gt' => time() - 86400 * 30 ] ] ) ),
        ];
    }

    /**
     * @param $aggregate
     * @return array
     */
    public function getTotalRequestCount($aggregate)
    {
        return [
            'day' => $this->mongoCollectionForAccess->count([ 'fromServer.hostname' => $aggregate['_id'], 'stamp' => [ '$gt' => time() - 86400 ] ] ),
            'week' => $this->mongoCollectionForAccess->count([ 'fromServer.hostname' => $aggregate['_id'], 'stamp' => [ '$gt' => time() - 86400 * 7 ] ] ),
            'month' => $this->mongoCollectionForAccess->count([ 'fromServer.hostname' => $aggregate['_id'], 'stamp' => [ '$gt' => time() - 86400 * 30 ] ] ),
        ];
    }

    /**
     * @return mixed
     */
    public function getRecentVisitors()
    {
        $visitors = $this->mongoCollectionForAccess->aggregate([
            '$group' => [
                '_id' => '$host',
                'time' => ['$last' => '$stamp'],
                'requestedPage' => ['$last' => '$request'],
                'device' => ['$last' => '$device'],
                'location' => ['$last' => '$location'],
                'status' => ['$last' => '$status'],
                'count' => ['$sum' => 1],
                ]
        ])['result'];

        foreach ($visitors as &$visitor) {
            $visitor['time'] = Carbon::createFromTimestamp($visitor['time'])->diffForHumans();
        }

        return $visitors;

    }

    /**
     * @param $aggregate
     * @return array
     */
    public function aggregateAnalytics($aggregate)
    {
        return [
            'uniqueVisits' => $this->getUniqueVisits($aggregate),
            'totalRequestCount' => $this->getTotalRequestCount($aggregate),
            'recentVisitors' => $this->getRecentVisitors()
        ];
    }

}
