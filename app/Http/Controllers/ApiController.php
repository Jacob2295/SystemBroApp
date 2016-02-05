<?php
namespace App\Http\Controllers;

use \Illuminate\Http\Request;
use GeoIp2\Database\Reader;

class ApiController extends Controller
{

    private $mongoCollection;

    public function __construct()
    {
        $this->mongoCollection = ( new \MongoClient() )->selectDB( 'SystemBro' )->selectCollection( 'stats' );
    }

    public function index()
    {

    }

    public function collect( Request $request )
    {
        $parser = new \Kassner\LogParser\LogParser();
        $parser->setFormat( $_ENV['ACCESS_LOG_FORMAT'] );

        foreach ( $request->accessLog as &$line ) {
            $userSpec = $parser->parse( $line );
            $userSpec->device = parse_user_agent( $userSpec->HeaderUserAgent );

            $city = new Reader( __DIR__ . '/../Database/GeoLite2-City.mmdb' );
            $country = new Reader( __DIR__ . '/../Database/GeoLite2-Country.mmdb' );


            $userSpec->location = [
                'city'    => $city->city( $userSpec->host )->city->name,
                'country' => $country->country( $userSpec->host )->country->name,
            ];

            $entry[] = $userSpec;
        }

        $this->mongoCollection->batchInsert( $request->toArray() );
    }

}