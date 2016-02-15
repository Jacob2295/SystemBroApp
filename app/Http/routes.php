<?php

use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$app->post('/collect', 'ApiController@collect');
$app->get('/retrieve', 'ApiController@RetrieveCollectedData');
$app->get('/getServers', 'ApiController@getServers');
$app->get('/addServer', 'ApiController@addServer');
$app->get('/', 'ApiController@index');
