<?php
echo "Start...";

use Workerman\Worker;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/include/Bootstrap.php';
require_once __DIR__ . '/include/models/Handler.php';

// Create a Websocket server
$ws_worker = new Worker('tcp://0.0.0.0:2347');

// 1 process
$ws_worker->count = 1;

global $app;
global $realmsg;

$app = new Bootstrap();

$app->blacklist = [];

$app->devices = [];
$app->connections = [];

$app->handler = new \models\Handler();


// Emitted when new connection come
$ws_worker->onConnect = function ($conn) {

    $conn->onWebSocketConnect =function($conn, $header){

     };

    echo "New connection\n";
};



// Emitted when data received
$ws_worker->onMessage = function ($from, $msg) {
	
    global $app;
    global $realmsg;
    $goon = false;
	

    if ($realmsg==""){
		$realmsg = $msg;
    } else{
		$realmsg .=$msg;
    }
	
    if (strpos($realmsg,"{")===false){
		$goon = true;
    } else {

		if (strpos($realmsg,',"ver":"1.A"')===false){
		} else {
			$goon = true;
		}
    }
 
    
    if ($goon){
		
		$data = json_decode($realmsg, true);
		$realmsg = $data['data'];
        $q = $app->request::parseRequest($from,$realmsg);
        $realmsg = "";
        if(!$q){

			$app->response::sendResponse($from,1);        		
		} else{

			$app->handler::processQ($from,$q);
		}
    }

};

// Emitted when connection closed

$ws_worker->onClose = function ($conn) {
	
    global $app;
    if($app->debug){ 
		$t = microtime(true); 
	}

    $id = (isset($conn->client_id))?$conn->client_id:0;
    unset($app->devices[$id]);
    unset($app->connections[$id]);

    if($app->debug){ echo "onClose: ".(microtime(true) - $t)."\n"; }
    echo "Connection closed\n";
};

 // Run worker

Worker::runAll();
$realmsg = "";