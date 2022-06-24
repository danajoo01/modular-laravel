<?php
namespace App\Libraries;
class Mail {
    private $url, $key;

    public function __construct($url, $key){
        $this->url = $url;
        $this->key = $key;
    }

    public function SendMail($body){
        $curl = curl_init();

        $url = $this->url;
        $key = $this->key;

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_ENCODING, "");
        curl_setopt($curl, CURLOPT_MAXREDIRS, 10);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("api_key: $key","content-type: application/json"));

        $response = curl_exec($curl);    
        curl_close($curl);
    }
}