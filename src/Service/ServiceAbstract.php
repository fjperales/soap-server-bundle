<?php

namespace PacoP\SoapServerLibs\Service;


abstract class ServiceAbstract
{

    abstract public function getWsdlFile() :?string;
    abstract public function getWsdlTemplate() :?string;
    abstract public function getUri() :?string;
    abstract public function getClassMap() :?array;
    abstract public function getClass() :object;
    abstract public function setRequest(string $request);
    abstract public function setResponse(string $response);
    abstract public function getResponse() :string;

    public function getSoapVersion() :?string
    {
        return SOAP_1_2;
    }
    public function getEncoding() :?string
    {
        return UTF8;
    }

    public function getCacheWsdl() :int
    {
        return  WSDL_CACHE_NONE;
    }

    public function getSendError() :bool
    {
        return false;
    }
}