<?php

require __DIR__ . '/../vendor/autoload.php';


use PacoP\SoapServerLibs\Service\SoapServer;
use PacoP\SoapServerLibs\Service\Service;

$service = new Service();

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