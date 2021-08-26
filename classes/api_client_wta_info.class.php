<?php
require_once('../lib/core.lib.php');

class api_client_wta_info{
    private $urlApi = 'https://wtaops.com/app/apiWtaOnline/';
    private $method = 'POST';
    private $auth=['users'=>'ILSBSYS','pass'=>'dkdZVC'];
    private $function ='';
    private $parameters = [];
    public static function initApi($urlApi=null){
        $intance=new self();
        if($urlApi){
            $intance->urlApi=$urlApi;
        }
        return $intance;
    }
    public function parameters($parameters)
    {
        $this->parameters = $parameters;
        return $this;
    }
    
    public function functions($function)
    {
        $this->function = $function;
        return $this;
    }
    
    public function method($method)
    {
        $this->method = $method;
        return $this;
    }
    public function callApi(){
        
        $urlConsumer 	= $this->urlApi.$this->function;
        $urlConsumer	= ($this->method=='GET')?$urlConsumer.'?'.http_build_query($this->parameters):$urlConsumer;
        
        static $token=false;
        $curl = curl_init();
        $headers['authorization']="Basic ".base64_encode($this->auth['users'] . ":" . $this->auth['pass']);
        if($token){
            $headers['TOKEN']=$token;
        }
        curl_setopt_array($curl, array(
            CURLOPT_URL => $urlConsumer,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $this->method,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => ($this->method=='POST')?json_encode($this->parameters):'',
            CURLOPT_HTTPHEADER => array_map(function($key,$value) {return "$key: $value";},array_keys($headers),$headers),
        ));
        $response = curl_exec($curl);
        // Retudn headers seperatly from the Response Body
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $headersResponse = explode("\r\n",substr($response, 0, $header_size));
        $headersResponse = array_reduce($headersResponse,
            function($response,$element){
                $val =explode(": ",$element);
                if(!empty($val[1])){
                    $response[$val[0]]=$val[1];
                }
                return $response;
            },[]);
        if($headersResponse['TOKEN']){
            $token=$headersResponse['TOKEN'];
        }
        $body = substr($response, $header_size);
        $err = curl_error($curl);
        curl_close($curl);
        return json_decode(trim($body),true)?:(json_decode(trim($err),true)?:$err);
    }
}

