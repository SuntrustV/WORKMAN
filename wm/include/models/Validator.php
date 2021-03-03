<?php


namespace models;


class Validator
{
    public static function clearData($data) {
        $data = trim(strip_tags($data));
        return $data;
    }

    public static function validateEmail($email) {
        return (filter_var($email, FILTER_VALIDATE_EMAIL) != false);
    }
    
    public static function hex_to_int($v){
        return hexdec($v);
    }
    public static function int_to_hex($v){
        return dechex($v);
    }

    public static function str_encrypt($msg, $key = 'key_response'){
        global $app;
        if(!$app->use_crypt){ return $msg;}

        $secure_data = $app->config->getCryptData();


        $cipher = $secure_data['cipher'];
        $key = $secure_data[$key];

        $ivlen = openssl_cipher_iv_length($cipher);
        $iv = openssl_random_pseudo_bytes($ivlen);

        $ciphertext_raw = openssl_encrypt(str_pad(utf8_encode($msg),16), $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);

        $ciphertext = base64_encode( $iv.$ciphertext_raw);

        return $ciphertext;

    }
    public static function str_decrypt($msg, $key = 'key_msg'){
        global $app;
        if(!$app->use_crypt){ return $msg;}

        $secure_data = $app->config->getCryptData();

        $cipher = $secure_data['cipher'];
        $key = $secure_data[$key];

        $c = base64_decode($msg);
        $ivlen = openssl_cipher_iv_length($cipher);

        $iv = substr($c, 0, $ivlen);
        $ciphertext_raw = substr($c, $ivlen);

        $original_msg = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);

        if(!$original_msg){
            $key = $secure_data['key_auth'];
            $original_msg = openssl_decrypt($ciphertext_raw, $cipher, $key, $options=OPENSSL_RAW_DATA, $iv);
        }

        return $original_msg;

    }


    public static function validatePassword($password){
        global $app;
        $config = $app->getConfig();
        $len = (isset($config['validator'], $config['validator']['password_length']))?
            $config['validator']['password_length'] : 6;
        if(!empty($password)) {
            if (strlen($password) < $len) {
                return "Your Password Must Contain At Least $len Characters!";
            }
            elseif(!preg_match("#[0-9]+#",$password)) {
                return "Your Password Must Contain At Least 1 Number!";
            }
            elseif(!preg_match("#[A-Z]+#",$password)) {
                return "Your Password Must Contain At Least 1 Capital Letter!";
            }
            elseif(!preg_match("#[a-z]+#",$password)) {
                return "Your Password Must Contain At Least 1 Lowercase Letter!";
            }
        }else{
            return "Password is required";
        }

        return true;
    }

    public static function tokenValidate($param, $token){
        global $app;
        $config = $app->getConfig();
        if(isset($config,$config['salt'])){
            return $token == md5($param.$config['salt']);
        }
        return false;
    }

    public static function clearNumber($c){
        return preg_replace('/\D/', '', $c);
    }

    public static function bin2hex($str) {
        $hex = "";
        $i = 0;
        do {
            $hex .= sprintf("%02x", ord($str{$i}));
            $i++;
        } while ($i < strlen($str));
        return $hex;
    }

    public static function hexToStr($hex){
        $string='';
        for ($i=0; $i < strlen($hex)-1; $i+=2){
            $string .= chr(hexdec($hex[$i].$hex[$i+1]));
        }
        return $string;
    }

    public static function binaryToString($binary)
    {
        $binaries = explode(' ', $binary);

        $string = null;
        foreach ($binaries as $binary) {
            $string .= pack('H*', dechex(bindec($binary)));
        }

        return $string;
    }

}