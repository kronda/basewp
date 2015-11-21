<?php

class ThriveOptimize {

    public $api_url;

    public function __construct() {
        $this->api_url = "http://imgapi.thrivethemes.com/";
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
        $result = wp_remote_post($url, array(
            'body' => $data,
            'timeout' => 20,
            'sslverify' => false
        ));
        if ($result instanceof WP_Error) {
            return array("message" => $result->get_error_message());
        }

        $body = wp_remote_retrieve_body($result);

        return $json === false ? $body : json_decode($body, true);
    }
}