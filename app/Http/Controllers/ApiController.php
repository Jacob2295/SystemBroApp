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
        return view('index');
    }

    public function getCollecteddata()
    {

    }

    public function anyCollect( Request $request )
    {
        dd($request->toArray());
        $parser = new \Kassner\LogParser\LogParser();
        $parser->setFormat( $_ENV['ACCESS_LOG_FORMAT'] );

        foreach ( $request->accessLog as &$line ) {
            $userSpec = $parser->parse( $line );
            $userSpec->device = parse_user_agent( $userSpec->HeaderUserAgent );

            $city = new Reader( database_path() . '/GeoLite2-City.mmdb' );

            $geoRecord =  $city->city( $userSpec->host );

            $userSpec->location = [
                'city'    => $geoRecord->city->name,
                'country' => $geoRecord->country->name,
            ];

            $entry[] = $userSpec;
        }

        $this->mongoCollection->batchInsert( $request->toArray() );
    }

}