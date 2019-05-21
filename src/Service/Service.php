<?php


namespace PacoP\SoapServerLibs\Service;


class Service implements ServiceInterface
{
    /** @var string */
    private $wsdlFile;
    /** @var string */
    private $wsdlTemplate;
    /** @var string */
    private $uri;
    /** @var array */
    private $classMap;
    /** @var object */
    private $class;
    /** @var string */
    private $request;
    /** @var string */
    private $response;
    /** @var int */
    private $soapVersion = SOAP_1_2;
    /** @var string */
    private $encoding = 'UTF8';
    /** @var int */
    private $cacheWsdl = WSDL_CACHE_NONE;
    /** @var bool */
    private $sendError = false;

    /**
     * @param string $wsdlFile
     */
    public function setWsdlFile(string $wsdlFile): void
    {
        $this->wsdlFile = $wsdlFile;
    }

    /**
     * @param string $wsdlTemplate
     */
    public function setWsdlTemplate(string $wsdlTemplate): void
    {
        $this->wsdlTemplate = $wsdlTemplate;
    }

    /**
     * @param string $uri
     */
    public function setUri(string $uri): void
    {
        $this->uri = $uri;
    }

    /**
     * @param array $classMap
     */
    public function setClassMap(array $classMap): void
    {
        $this->classMap = $classMap;
    }

    /**
     * @param object $class
     */
    public function setClass(object $class): void
    {
        $this->class = $class;
    }

    /**
     * @param int $soapVersion
     */
    public function setSoapVersion(int $soapVersion): void
    {
        $this->soapVersion = $soapVersion;
    }

    /**
     * @param string $encoding
     */
    public function setEncoding(string $encoding): void
    {
        $this->encoding = $encoding;
    }

    /**
     * @param int $cacheWsdl
     */
    public function setCacheWsdl(int $cacheWsdl): void
    {
        $this->cacheWsdl = $cacheWsdl;
    }

    /**
     * @param bool $sendError
     */
    public function setSendError(bool $sendError): void
    {
        $this->sendError = $sendError;
    }



    public function getWsdlFile(): ?string
    {
        return $this->wsdlFile;
    }

    public function getWsdlTemplate(): ?string
    {
        return $this->wsdlTemplate;
    }

    public function getUri(): ?string
    {
        return $this->uri;
    }

    public function getClassMap(): ?array
    {
        return $this->classMap;
    }

    public function getClass(): object
    {
        return $this->class;
    }

    public function setRequest(string $request)
    {
        $this->request = $request;
    }

    public function setResponse(string $response)
    {
        $this->response = $response;
    }

    public function getResponse(): string
    {
        return $this->response;
    }

    public function getSoapVersion(): ?int
    {
        return $this->soapVersion;
    }

    public function getEncoding(): ?string
    {
        return $this->encoding;
    }

    public function getCacheWsdl(): int
    {
        return $this->cacheWsdl;
    }

    public function getSendError(): bool
    {
        return $this->sendError;
    }
}