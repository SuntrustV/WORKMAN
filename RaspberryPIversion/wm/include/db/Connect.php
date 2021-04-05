<?php


namespace db;

class Connect
{
    private static $connect = null;

    
    public static function getConnect($config, $new = false) {
        $connect = null;
        if(is_null(self::$connect) && isset($config['db']) && isset($config['db']['connector'])){
            if(file_exists(__DIR__.'/'.$config['db']['connector'].'.php')){
                require_once (__DIR__.'/'.$config['db']['connector'].'.php');
                $class = 'db\\'.$config['db']['connector'];
                $connect = $class::connect($config['db']);

                if($new){
                    return $connect;
                }
            }
        }elseif(is_null(self::$connect)){
            throw new \Exception('Not db connection');
        }

        if(!is_null($connect)){
            self::$connect = $connect;
        }

        return self::$connect;
    }
}