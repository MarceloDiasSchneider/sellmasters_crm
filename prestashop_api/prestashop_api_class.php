<?php

class prestashopApiClass
{
    public $url;
    public $virtual_uri;
    public $key;
    public $password;
    public $method;
    public $postFields;

    function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    public function resquest_prestashop_api()
    {
        $curl = curl_init();
        $options = array(
            CURLOPT_URL => $this->url . $this->virtual_uri,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $this->method,
            CURLOPT_USERPWD => $this->key . ":",
            CURLOPT_HTTPHEADER => array(
                'output_format: JSON',
            ),
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
