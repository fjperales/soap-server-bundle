<?php

$context = stream_context_create([
    'ssl' => [
        // set some SSL/TLS specific options
        'verify_peer' => false,
        'verify_peer_name' => false,
        'allow_self_signed' => true
    ]
]);

$client = new SoapClient(__DIR__ . "/wsdl/ping.wsdl",array(
    'cache_wsdl'  => WSDL_CACHE_NONE,
    'exceptions' => 1,
    'trace' => 1,
    'stream_context' => $context,
    'soap_version'=> SOAP_1_2 ));
$client->__setLocation("https://localhost/soap-server-wsse-cert.php/ping");

try{
    $request = new stdClass();
    $request->valor = "Hello World!!!";
    $client->Ping($request);

    echo $client->__getLastResponse();

}catch (\SoapFault $e){
    echo $e->getMessage();
}


