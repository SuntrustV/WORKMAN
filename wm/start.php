<?php
echo "Start...";

//$st ='[{"id": 1, "datetime": "2021-02-26 11:30:21+02", "software_version": "22", "hardware_version": "33", "c_limit": 44, "c_times": 55, "c_brigh": 66, "c_brigG": 77, "c_brigR": 88, "c_distn": "99", "c_objec": "0", "c_memor": "11", "c_minin": 22, "c_maxin": 333, "c_limRG": "44", "c_frequ": "55", "c_targt": "66", "c_filtr": "77", "c_tzero": 88, "c_alive": 999, "rpi_imei": "0", "rpi_name": "1", "rpi_timezone": 2, "rpi_set_time": "3", "rpi_gps": "4", "rpi_limit": 5, "rpi_ip_main": "6", "rpi_ip_light": "7", "rpi_smtpadress": "8", "rpi_smtplogin": "9", "rpi_smtppassword": "0", "rpi_radarminspeed_shot": 1, "rpi_radarmaxspeed_shot": 2, "rpi_delay": 3, "rpi_quant": 4, "rpi_freq": 5, "rpi_email": "6", "rpi_cloud": "7", "rpi_radarmaxspeed": 8, "rpi_radarminspeed": 9, "rpi_only_email": 0, "rpi_storage_address": "1", "created_at": "2021-02-2", "device_id": 3}, {"id": 2, "datetime": "2021-02-26 11:30:21+02", "software_version": "1", "hardware_version": "11", "c_limit": 22, "c_times": 33, "c_brigh": 44, "c_brigG": 55, "c_brigR": 66, "c_distn": "7", "c_objec": "88", "c_memor": "99", "c_minin": 0, "c_maxin": 111, "c_limRG": "22", "c_frequ": "33", "c_targt": "44", "c_filtr": "55", "c_tzero": 666, "c_alive": 777, "rpi_imei": "88", "rpi_name": "99", "rpi_timezone": 0, "rpi_set_time": "1", "rpi_gps": "2", "rpi_limit": 3, "rpi_ip_main": "4", "rpi_ip_light": "5", "rpi_smtpadress": "6", "rpi_smtplogin": "7", "rpi_smtppassword": "8", "rpi_radarminspeed_shot": 9, "rpi_radarmaxspeed_shot": 0, "rpi_delay": 1, "rpi_quant": 2, "rpi_freq": 3, "rpi_email": "4", "rpi_cloud": "5", "rpi_radarmaxspeed": 6, "rpi_radarminspeed": 7, "rpi_only_email": 8, "rpi_storage_address": "9", "created_at": "2021-02-26 11:30:23+2", "device_id": 1}]';


use Workerman\Worker;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/include/Bootstrap.php';
require_once __DIR__ . '/include/models/Handler.php';

// Create a Websocket server
$ws_worker = new Worker('tcp://0.0.0.0:2346');

// 1 process
$ws_worker->count = 1;

global $app;

$app = new Bootstrap();

$app->blacklist = [];

$app->devices = [];
$app->connections = [];

$app->handler = new \models\Handler();


// Emitted when new connection come
$ws_worker->onConnect = function ($conn) {

    $conn->onWebSocketConnect =function($conn, $header){

//     if($conn->getParams){
//     }

     };

    echo "New connection\n";
};



// Emitted when data received
$ws_worker->onMessage = function ($from, $msg) {
    global $app;
 
    $q = $app->request::parseRequest($from,$msg);
   
    if(!$q){

        $app->response::sendResponse($from,1);
    } else{

        $app->handler::processQ($from,$q);
    }

};

// Emitted when connection closed

$ws_worker->onClose = function ($conn) {
    global $app;
    if($app->debug){ $t = microtime(true); }

    $id = (isset($conn->client_id))?$conn->client_id:0;
    unset($app->devices[$id]);
    unset($app->connections[$id]);

    if($app->debug){ echo "onClose: ".(microtime(true) - $t)."\n"; }
    echo "Connection closed\n";
};

    // Run worker

    Worker::runAll();
