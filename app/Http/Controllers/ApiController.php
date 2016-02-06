<?php
namespace App\Http\Controllers;

use App\Models\Stats;
use \Illuminate\Http\Request;
use App\Models\LoggingModel;

class ApiController extends Controller
{

    private $mongoCollection;

    private $loggingModel;

    public function __construct()
    {
        $this->mongoCollection = ( new \MongoClient() )->selectDB( 'SystemBro' );
        $this->loggingModel = new LoggingModel();
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
        parse_str( $request->getContent(), $request );


        if ( count( $request['accessLog'] ) > 0 ) {
            $this->loggingModel->insertAccessLogging( $request['accessLog'] );
        }
        unset( $request['accessLog'] );

        if ( count( $request['errorLog'] ) > 0 ) {
            $this->loggingModel->insertErrorLogging( $request['errorLog'] );
        }
        unset( $request['errorLog'] );

        $this->mongoCollection->selectCollection( 'stats' )->insert( $request );

        return true;
    }

}