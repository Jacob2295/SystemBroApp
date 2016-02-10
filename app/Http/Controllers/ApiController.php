<?php
namespace App\Http\Controllers;

use App\Models\Stats;
use \Illuminate\Http\Request;
use App\Models\Logging;

/**
 * Class ApiController
 * @package App\Http\Controllers
 */
class ApiController extends Controller
{

    /**
     * @var \MongoDB
     */
    private $mongoCollection;

    /**
     * @var Logging
     */
    private $loggingModel;

    /**
     * @var Stats
     */
    private $stats;

    /**
     * ApiController constructor.
     */
    public function __construct()
    {
        $this->mongoCollection = ( new \MongoClient() )->selectDB( 'SystemBro' );
        $this->loggingModel = new Logging();
        $this->stats = new Stats();
    }

    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view( 'index' );
    }

    /**
     *
     */
    public function RetrieveCollectedData()
    {

    }

    /**
     * @param Request $request
     *
     * @return string
     */
    public function collect( Request $request )
    {
        parse_str( $request->getContent(), $payload );

        $payload['from'] = [
            'hostname' => $payload['hostname'],
            'ip'       => $request->ip()
        ];

        $this->loggingModel->insertAccessLogging( $payload['accessLog'], $payload['from'] );

        $this->loggingModel->insertErrorLogging( $payload['errorLog'], $payload['from'] );


        unset( $payload['accessLog'], $payload['errorLog'] );

        $payload['serverCollectedFromIP'] = $request->ip();
        $this->stats->insert( $payload );

        return 'inserted';
    }

}