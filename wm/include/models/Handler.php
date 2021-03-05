<?php

namespace models;

require_once __DIR__ . '/Validator.php';
require_once __DIR__ . '/Device.php';


class Handler
{

    public static function processQ($from,$q){
        global $app;


        if ($q['method_id']==1) {

            //print_r($q);
            $device_key = $q['device_key'];
            $pincode = $q['pincode'];
            $v = $q["v"];
            $RPItime = $q["RPItime"];
            $result = $app->db->query("select * from smarttsn.devices where device_sn='$device_key' and device_pin= '$pincode'");
            if (count($result) == 0) {
                $from->close();

            } else {
                $device_id = $result[0]['id'];
                $message_id = rand(1000000000,9999999999);
                $st = "insert into smarttsn.log_connect (created_at, device_id, v,system_time) values ('".date("Y-m-d H:i:s")."', '$device_id', $v, '$RPItime') ";
                //echo $st;
                $app->db->query($st);
                $app->response::sendResponse($from,200,1,$message_id);
            }

        } else {
            //print_r($q);
            $message_id = $q["message_id"];//rand(1000000000,9999999999);
            if ($message_id==0) {
                // Не был сгенерирован message_id при авторизации, то есть её не было ( => разрыв
                $app->response::sendResponse($from, 1, $q['method_id'], $message_id);
                $from->close();
            } else {
                $st = $q["data"];
                $data = json_decode($st, true);

                $tableName = "smarttsn." . $data["tableName"];
                $data2 = $data["data"];

                foreach ($data2 as $record) {

                    $i = 0;
                    $s1 = "(";
                    $s2 = "(";
                    $record['synchro'] = $message_id . ' ' . date("Y-m-d H:i:s");
                    foreach ($record as $key => $val) {

                        $i++;
                        if ($i > 1) {
                            $s1 .= ",";
                            $s2 .= ",";
                        }

                        if (strtolower($key) != $key) {
                            $s1 .= '"' . $key . '"';
                        } else {
                            $s1 .= $key;
                        }
                        $s2 .= "'" . $val . "'";
                    }

                    $s1 .= ")";
                    $s2 .= ")";

                    $query = "insert into $tableName $s1 values $s2";

                    $app->db->query($query);
                    $app->response::sendResponse($from, 200, $q['method_id'], $message_id);

                }
            }
        }

/*
        if($q['method_id'] == 1){
            $device = new Device();
            if($d = $device->check_access($q)){
                $from->client_id = $d['id'];
                $app->connections[$d['id']] = $from;
                $app->devices[$d['id']] = $d;
                unset($device);
                $app->response::sendResponse($from,0, $q['method_id'], $q['message_id']);
            }
            else{
                $app->response::sendResponse($from,401, $q['method_id'], $q['message_id']);
                unset($device);
                $from->close();
            }
        }elseif(!$from->client_id || !isset($app->connections[$from->client_id])){
            $app->response::sendResponse($from,401, $q['method_id'], $q['message_id']);
            unset($device);
            $from->close();
        }else{

            switch ($q['method_id']){
                case 2:
    //                $app->response::sendResponse($from,404, $q['method_id'], $q['message_id']);
                    break;
                case 3:
                    $device = new Device();
                    $d = $device->load($from->client_id);
                    $device->setData($d,$q);
                    unset($device);
                    $app->response::sendResponse($from,0, $q['method_id'], $q['message_id']);
                    break;
                default:
                    $app->response::sendResponse($from,404, $q['method_id'], $q['message_id']);
                    break;
            }
        
        }

*/

    }



}
