<?php
namespace App\Http\Controllers;

use App\Models\Stats;
use \Illuminate\Http\Request;
use App\Models\LoggingModel;

class ApiController extends Controller
{

    private $mongoCollection;

    private $loggingModel;

    private $stats;

    public function __construct()
    {
        $this->mongoCollection = ( new \MongoClient() )->selectDB( 'SystemBro' );
        $this->loggingModel = new LoggingModel();
        $this->stats = new Stats();
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
        parse_str( $request->getContent(), $payload );

        if ( count( $payload['accessLog'] ) > 0 ) {
            $this->loggingModel->insertAccessLogging( $payload['accessLog'] );
        }
        unset( $payload['accessLog'] );

        if ( isset($payload['errorLog']) && count( $payload['errorLog'] ) > 0 ) {
            $this->loggingModel->insertErrorLogging( $payload['errorLog'] );
            unset( $payload['errorLog'] );
        }

        $this->stats->insert( $payload );

        return 'inserted';
    }

}