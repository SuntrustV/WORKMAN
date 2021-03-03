<?php



class Config
{
    private $db;
    private $cache;
    private $debug;


    public function __construct() {
        $this->db = [
            'connector' => 'pgsql',
            'host' => 'localhost',
            'port' => '5432',
            'user' => 'desolarcloud',
            'password' => 'genesis',
            'dbname' => 'smarttsn',
        ];
        $this->cache = [
            'class' => 'Redis',
            'host' => '127.0.0.1',
            'port' => '6379',
            'user' => '',
            'password' => '',
        ];
        $this->crypt_data = [
            'cipher' => "AES-192-CBC",
            'key_auth' => "0123456789abcdefghijklmn",
            'key_msg' => "01ghijklmnob123456123456",
            'key_response' => "01ghijklmnob123456123456",
            'key_3' => "01ghijklmnob123456123456",
        ];

        $this->debug = true;
        $this->use_crypt = true;
    }
    public function getDB(){
        return $this->db;
    }
    public function getCache(){
        return $this->cache;
    }
    public function getCryptData(){
        return $this->crypt_data;
    }
    public function getDebug(){
        return $this->debug;
    }
}