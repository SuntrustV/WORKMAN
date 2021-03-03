<?php

require_once 'Config.php';
require_once __DIR__ . '/db/PostgresDb.php';
require_once __DIR__ . '/models/Request.php';
require_once __DIR__ . '/models/Response.php';

use db\PostgresDb;
use models\Request;
use models\Response;
//use Predis\Client;


class Bootstrap
{
    public static $keyPrefix;

    /**
     * @param $config
     * @param $socket_id
     */


    public function __construct() {
        // Config
        $this->cache = false;
        $this->config = new Config();
        $this->debug = $this->config->getDebug();
        $this->use_crypt = $this->config->use_crypt;

        /*
        // Cache
        $cache_config = $this->config->getCache();
        $cache_obj = new $cache_config['class'];
        if(!$cache_obj){ echo "Cache start failed."; exit;}
        $cache_obj->connect($cache_config['host'], $cache_config['port']);
        $this->cache = $cache_obj;
        */

        // DB
        $db_config = $this->config->getDB();


        $this->db = new PostgresDb($db_config['dbname'], $db_config['host'], $db_config['port'], $db_config['user'], $db_config['password']);

        if (empty($this->db->pdo())) {
            echo "DB start failed.";
            exit;
        }

        // Arrays
        $this->devices = [];
        $this->request = new Request();
        $this->response = new Response();


    }





}
