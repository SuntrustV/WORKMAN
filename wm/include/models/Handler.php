<?php

namespace models;

require_once __DIR__ . '/Validator.php';
require_once __DIR__ . '/Device.php';


class Handler
{

    public static function processQ($from,$q){
        global $app;


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
    }



}
