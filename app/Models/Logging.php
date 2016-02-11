<?php
namespace App\Models;
use GeoIp2\Database\Reader;

/**
 * Class LoggingModel
 * @package App\Models
 */
class Logging {

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
    public function getUniqueVisits()
    {
        return [
            'day' => count( $this->mongoCollectionForAccess->distinct( 'host', [ 'stamp' => [ '$gt' => time() - 86400 ] ] ) ),
            'week' => count( $this->mongoCollectionForAccess->distinct( 'host', [ 'stamp' => [ '$gt' => time() - 86400 * 7 ] ] ) ),
            'month' => count( $this->mongoCollectionForAccess->distinct( 'host', [ 'stamp' => [ '$gt' => time() - 86400 * 30 ] ] ) ),
        ];
    }

}
