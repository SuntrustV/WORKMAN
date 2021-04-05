<?php


namespace models;


class Device
{
    private $db;
    public static $table = 'device';

    public function __construct() {
        global $app;
        $this->db = $app->db;
        $this->response = $app->response;
    }

    public function check_access($q){

        $m_exist = $this->db->where('device_key',intval($q['device_key']))->where('pincode',intval($q['pincode']))->getOne(self::$table);
        if(empty($m_exist)){ return false; }
        if(isset($m_exist['status']) && $m_exist['status']==9){ return false; }
        if(isset($m_exist['status']) && $m_exist['status']==10){ return false; }

        return self::clear_obj($m_exist);
    }

    public function setData($d,$q){
        if(isset($d['id']) && isset($q['data'])){
            $this->db->insert('watchdog', ['data' => $q['data'],'device_id' => $d['id'], 'created_at' => time(), 'updated_at' => time()]);
        }

        return true;
    }

    public function load($id){
        global $app;

        if($app->cache && !empty($d=$app->cache->get('devices_'.$id))){
            $m_exist =  unserialize($d);
        }else{
            $m_exist = $this->db->where('id',intval($id))->getOne(self::$table);
        }
        if(empty($m_exist)){ return false; }
        if(isset($m_exist['status']) && $m_exist['status']==9){ return false; }
        if(isset($m_exist['status']) && $m_exist['status']==10){ return false; }
        if($app->cache){ $app->cache->set('devices_'.$id,serialize($m_exist)); }

        return self::clear_obj($m_exist);
    }



    public static function clear_obj($u){
        if(is_array($u)){
            $ret = ['id'=>0, 'status'=>0, 'device_key'=>0];

            foreach ($ret as $k=>$v){
                $ret[$k] = ($k=='data')?strval($u[$k]):floatval($u[$k]);
            }
            return $ret;
        }else{
            return [];
        }

    }






}