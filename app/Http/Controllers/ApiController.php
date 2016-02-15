<?php
namespace App\Http\Controllers;

use App\Models\Stats;
use \Illuminate\Http\Request;
use App\Models\Analytics;

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
     * @var Analytics
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
        $this->loggingModel = new Analytics();
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
        return $this->stats->grabStats();
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    public function collect( Request $request )
    {
        parse_str( $request->getContent(), $payload );

        if ($this->mongoCollection->selectCollection('allowedServers')->count() == 0) {
            $this->mongoCollection->selectCollection('allowedServers')->insert([
                'type' => 'allowedServerList',
                'allowedServers' => [
                    $payload['hostname']
                ]
            ]);
        } elseif (!in_array($payload['hostname'],$this->mongoCollection->selectCollection('allowedServers')->findOne(['type' => 'allowedServerList'])['allowedServers'])) {
            return 'permission denied';
        }

        $payload['fromServer'] = [
            'hostname' => $payload['hostname'],
            'ip'       => $request->ip()
        ];

        if (isset($payload['accessLog'])) {
            $this->loggingModel->insertAccessLogging( $payload['accessLog'], $payload['fromServer'], $payload['parseString'] );
        }

        unset( $payload['accessLog'], $payload['errorLog'] );

        $payload['serverCollectedFromIP'] = $request->ip();
        $this->stats->insert( $payload );

        return 'inserted';
    }

    public function addServer(Request $request)
    {
        return $this->mongoCollection->selectCollection('allowedServers')->update(['type' => 'allowedServeList'], [
            '$push' => [
                'allowedServers' => $request->hostname
            ]
        ], ['upsert'=>1]);
    }

}