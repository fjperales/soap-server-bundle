<?php


namespace PacoP\SoapServerLibs\Service;


interface ServiceInterface
{
    public function getWsdlFile() :?string;
    public function getWsdlTemplate() :?string;
    public function getUri() :?string;
    public function getClassMap() :?array;
    public function getClass() :object;
    public function setRequest(string $request);
    public function setResponse(string $response);
    public function getResponse() :string;
    public function getSoapVersion() :?int;
    public function getEncoding() :?string;
    public function getCacheWsdl() :int;
    public function getSendError() :bool;
}