<?php
namespace App\Http\Controllers;

use \Illuminate\Http\Request;
use GeoIp2\Database\Reader;
use Illuminate\Support\Facades\Log;
use Monolog\Logger;

class ApiController extends Controller
{

    private $mongoCollection;

    public function __construct()
    {
        $this->mongoCollection = ( new \MongoClient() )->selectDB( 'SystemBro' )->selectCollection( 'stats' );
    }

    public function index()
    {
        return view( 'index' );
    }

    public function RetrieveCollectedData()
    {

    }

    public function collect( Request $request )
    {
        parse_str($request->getContent(),$request);
        $parser = new \Kassner\LogParser\LogParser();
        $parser->setFormat( '%h %l %u %t "%r" %>s %O "%{Referer}i" \"%{User-Agent}i"' );

        if ( count( $request['accessLog'] ) > 0 ) {
            foreach ( $request['accessLog'] as &$line ) {
                $userSpec = $parser->parse( $line );
                $request['device'] = parse_user_agent( $userSpec->HeaderUserAgent );

                $city = new Reader( database_path() . '/GeoLite2-City.mmdb' );

                $geoRecord = $city->city( $userSpec->host );

                $request['location'] = [
                    'city'    => $geoRecord->city->name,
                    'country' => $geoRecord->country->name,
                ];
            }
        }
        //TODO: part out error logs to a different collection

        return $this->mongoCollection->insert( $request );
    }

}