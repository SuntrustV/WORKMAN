<?php
namespace models;

require_once __DIR__ . '/Validator.php';

class Response
{
    public static function errorList($id=0){
        $errs = [
            0 => "No errors",
            1 => "Incorrect request string",
            401 => "Incorrect AUTH data",
            403 => "Access denied",
            404 => "Method is not recognized",
        ];
        return (isset($errs[$id]))?$id:1000;
    }

    public static function methodList($id=0){

//        0x01 --- CONNECT,
//        0x02 --- CONNECT_ACK,
//        0x03 --- DATA,
//        0x04 --- COMMAND,
//        0x05 --- COMMAND_ACK


        $methods = [
            1 => "CONNECT",
            2 => "CONNECT_ACK",
            3 => "DATA",
            4 => "COMMAND",
            5 => "COMMAND_ACK",

        ];

        return (isset($methods[$id]))?$id:0;
    }


    public static function sendResponse($conn, $error_id, $method_id=0, $message_id=0, $value=''){

        global $app;

        $method = Validator::int_to_hex(intval(self::methodList($method_id)));
        $message = Validator::int_to_hex($message_id);
        $error = Validator::int_to_hex(intval(self::errorList(intval($error_id))));

        $ret = $method."/".$message_id."/".$error."/".$value;
        $key = ($method == 1)?'key_auth':'key_msg';
        $ret_msg = Validator::str_encrypt($ret,$key);

        $conn->send($ret_msg);

    }

}