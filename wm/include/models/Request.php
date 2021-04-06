<?php

namespace models;

require_once __DIR__ . '/Validator.php';




class Request
{

    // public $method_id;
    // public $msg_id;
    // public $err_id;
    // public $pincode;
    // public $data;

    public static function parseRequest($conn,$querystring){
        
        $q = false;
        
        
     if(!$conn || !$querystring){ return false;}else{

            $msg = Validator::str_decrypt($querystring);
           
            
            if(!is_string($msg)){ return false;}
                 
            $msg_arr = explode("/",$msg);
            if(!is_array($msg_arr) || !count($msg_arr)){ return false;}
            
            
            if(isset($msg_arr[0]) ){ $q['method_id'] = intval($msg_arr[0]);}else{ return false;}
            if(isset($msg_arr[1]) ){ $q['message_id'] = intval($msg_arr[1]);}else{ return false;}
            if(isset($msg_arr[2]) ){ $q['error_id'] = intval($msg_arr[2]);}else{ return false;}
            if($q['method_id'] == 1){ 
                if(isset($msg_arr[3]) ){ $q['pincode'] = intval($msg_arr[3]);}else{ return false;}
                if(isset($msg_arr[4]) ){ $q['device_key'] = intval($msg_arr[4]);}else{ return false;}
                if(isset($msg_arr[5]) ){ $q['v'] = $msg_arr[5];}else{ return false;}
                if(isset($msg_arr[6]) ){ $q['RPItime'] = $msg_arr[6];}else{ return false;}
                $q['data'] = '';
            }else{
                $data = '';
                unset($msg_arr[0]);unset($msg_arr[1]);unset($msg_arr[2]);
                $q['data'] = implode("/",$msg_arr);
            }
                      
        }      
        
        return $q;
            
    }
    
    

    public static function checkDevice($sys,$q){
        global $app;

        $authkey = Request::validate_txt($q['authkey']);

        //ADD the authkey validation

        return 1;

        return false;
    }

    


    public static function validate_txt($text){
        return trim(strip_tags(Request::check_plain($text)));
    }
    public static function check_plain($text){
        return Request::validate_utf8($text) ? htmlspecialchars($text, ENT_QUOTES) : '';
    }
    public static function validate_utf8($text) {
        if (strlen($text) == 0) {
            return TRUE;
        }
        return (preg_match('/^./us', $text) == 1);
    }
    

}