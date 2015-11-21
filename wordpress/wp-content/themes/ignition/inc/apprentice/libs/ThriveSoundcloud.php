<?php

class ThriveSoundcloud {

    public $api_url;

    public function __construct() {
        $this->api_url = "http://soundcloud.com/oembed";
    }

    public function url($opts = array()) {
        $data = http_build_query($opts);

        $response = self::request($data, $this->api_url);

        return $response;
    }

    public function send_response($opts, $url) {
        $data = http_build_query($opts);

        log_api("-API SEND RESPONSE-" . $url . "-params" . implode($opts));

        $response = self::request($data, $url, false);

        return $response;
    }

    private function request($data, $url, $json = true) {
        if (!$this->_isCurl()) {
            return array("message" => "Curl library not installed.");
        }
        
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        $response = curl_exec($curl);
        
        $rez = json_decode($response);
        
        curl_close($curl);

        return $rez;
    }

    private function _isCurl() {
        return function_exists('curl_version');
    }

}