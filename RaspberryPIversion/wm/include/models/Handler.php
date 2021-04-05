<?php

namespace models;

require_once __DIR__ . '/Validator.php';
require_once __DIR__ . '/Device.php';


class Handler
{

    public static function processQ($from,$q){
		
        global $app;

        if ($q['method_id']==1) {

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

                $app->db->query($st);
                $app->response::sendResponse($from,200,1,$message_id);
            }

        } else {

            $message_id = $q["message_id"];
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

    }



}
