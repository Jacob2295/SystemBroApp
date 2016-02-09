<?php
namespace App\Models;
use GeoIp2\Database\Reader;
class LoggingModel {

    public function __construct()
    {
        $this->mongoCollectionForErrors = ( new \MongoClient() )->selectDB( 'SystemBro' )->selectCollection( 'errorLogs' );
        $this->mongoCollectionForAccess = ( new \MongoClient() )->selectDB( 'SystemBro' )->selectCollection('accessLogs');
    }

    public function insertErrorLogging( array $elogs )
    {
        $elogs['createdAt'] = time();
        $this->mongoCollectionForErrors->batchInsert($elogs);
    }

    public function insertAccessLogging( array $alogs )
    {
        $parser = new \Kassner\LogParser\LogParser();
        $parser->setFormat( '%h %l %u %t "%r" %>s %O "%{Referer}i" \"%{User-Agent}i"' );
        $toBeInserted = [];
        foreach ( $alogs as $line ) {
            $userSpec = $parser->parse( $line );
            $userSpec->device = parse_user_agent( $userSpec->HeaderUserAgent );

            $city = new Reader( database_path() . '/GeoLite2-City.mmdb' );

            $geoRecord = $city->city( $userSpec->host );

            $userSpec->location = [
                'city'    => $geoRecord->city->name,
                'country' => $geoRecord->country->name,
            ];
            $userSpec->createdAt = time();
            $toBeInserted[] = $userSpec;
        }

        $this->mongoCollectionForAccess->batchInsert($toBeInserted);
    }

    public function getUniqueVisits()
    {
        return [
            'day' => count( $this->mongoCollectionForAccess->distinct( 'host', [ 'stamp' => [ '$gt' => time() - 86400 ] ] ) ),
            'week' => count( $this->mongoCollectionForAccess->distinct( 'host', [ 'stamp' => [ '$gt' => time() - 86400 * 7 ] ] ) ),
            'month' => count( $this->mongoCollectionForAccess->distinct( 'host', [ 'stamp' => [ '$gt' => time() - 86400 * 30 ] ] ) ),
        ];
    }

}
