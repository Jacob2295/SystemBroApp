<?php
namespace App\Models;
use GeoIp2\Database\Reader;
class LoggingModel {

    public function __construct()
    {
        $this->mongoCollection = ( new \MongoClient() )->selectDB( 'SystemBro' );
    }

    public function insertErrorLogging( array $elogs )
    {
        $this->mongoCollection->selectCollection( 'errorLogs' )->batchInsert($elogs);
    }

    public function insertAccessLogging( array $alogs )
    {
        $parser = new \Kassner\LogParser\LogParser();
        $parser->setFormat( '%h %l %u %t "%r" %>s %O "%{Referer}i" \"%{User-Agent}i"' );
        $toBeInserted = [];
        foreach ( $alogs as $line ) {
            $userSpec = $parser->parse( $line );
            $userStat['device'] = parse_user_agent( $userSpec->HeaderUserAgent );

            $city = new Reader( database_path() . '/GeoLite2-City.mmdb' );

            $geoRecord = $city->city( $userSpec->host );

            $userStat['location'] = [
                'city'    => $geoRecord->city->name,
                'country' => $geoRecord->country->name,
            ];
            $userStat['host'] = $userSpec->host;
            $toBeInserted[] = $userStat;
        }

        $this->mongoCollection->selectCollection('accessLogs')->batchInsert($toBeInserted);
    }

}
