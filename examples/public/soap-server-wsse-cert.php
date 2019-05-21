<?php

require __DIR__ . '/../vendor/autoload.php';


use PacoP\SoapServerLibs\Service\SoapServer;
use PacoP\SoapServerLibs\Service\ServiceWsseCert;

$privateKey = __DIR__ . "/../examples/certs/priv.pem";
$publicKey = __DIR__ . "/../examples/certs/pub.pem";
$passKey = "adm2013";

$service = new ServiceWsseCert($privateKey, $publicKey, $passKey);

$service->setWsdlFile(__DIR__ . "/wsdl/ping.wsdl");
$service->setClass(new PingPong());
$service->setCacheWsdl(WSDL_CACHE_NONE);

$soapServer = new SoapServer();


$soapServer->handle($service);

class PingPong
{
    public function Ping($arguments)
    {
        return new PingResponseType($arguments->valor);
    }
}

class PingResponseType
{
    /**
     * @var string
     */
    public $valor;

    public function __construct($valor)
    {
        $this->valor = $valor;
    }
}