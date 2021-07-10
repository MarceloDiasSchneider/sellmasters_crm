<?php

class woocommerceApiClass
{
    public $api_url;
    public $consumer_key;
    public $consumer_secret;
    public $api_version;
    public $method;
    public $resource;
    public $postFields;

    function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function woocommerceApi()
    {
        $curl = curl_init();
        $options = array(
            CURLOPT_URL => $this->api_url . $this->api_version . $this->resource,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $this->method,
            // CURLOPT_USERPWD => $this->consumer_key . ":" . $this->consumer_secret,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Basic Y2tfYTdkMzI1YTBjN2E4NzdkOWY3MWJiZjBhZDVkOWRiYmUyNzY4Mjg3YTpjc184ZjlhYzdhOTIyODhlZWViNDllNTk5N2Q4YjU0NzU0NjAwYjU4Yzlm',
            ),
            CURLOPT_POSTFIELDS =>'{"name":"CAPPELLI"}',
        );
        if( $this->method == 'POST' || $this->method == 'PUT') {
            $options[CURLOPT_POSTFIELDS] = $this->postFields;
            
        }
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }
}
