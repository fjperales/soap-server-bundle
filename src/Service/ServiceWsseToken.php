<?php


namespace PacoP\SoapServerLibs\Service;


class ServiceWsseToken extends Service
{
    public function setRequest($request)
    {
        $this->checkSecurity($request);
        parent::setRequest($request);
    }

    private function checkSecurity($request): void
    {
        $xml = new \SimpleXMLElement($request);
        $xml->registerXPathNamespace('soapenv', 'http://schemas.xmlsoap.org/soap/envelope/');
        $xml->registerXPathNamespace('wsse', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd');
        $securityHeaderNode= $xml->xpath('//soapenv:Envelope/soapenv:Header/wsse:Security/wsse:UsernameToken');
        if($securityHeaderNode){
            $securityHeader= $securityHeaderNode[0];
            $username = (string) $securityHeader->xpath("//wsse:Username")[0];
            $password = (string) $securityHeader->xpath("//wsse:Password")[0];
        }
    }
}