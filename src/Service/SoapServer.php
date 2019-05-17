<?php

namespace PacoP\SoapServerLibs\Service;

use Zend\Uri\UriFactory;
use Zend\Uri\Uri;
use Symfony\Component\HttpFoundation\Response;

class SoapServer
{
    /**
     * @var Uri
     */
    private $uri;

    /**
     * @var string
     */
    private $serviceName;

    public function __construct()
    {
        $this->uri = UriFactory::factory("$_SERVER[REQUEST_SCHEME]://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
        $this->serviceName = str_replace("/", "", $this->uri->getPath());
    }

    public function handle(ServiceAbstract $service)
    {
        if($service->getWsdlFile() and !$service->getUri()){
            throw new \Exception("For a WSDL mode the wsdl file is required");
        }elseif(!$service->getWsdlFile() and $service->getUri()){
            throw new \Exception("For a NO WSDL mode the uri is required");
        }

        if($service->getWsdlFile() and !file_exists($service->getWsdlFile())){
            if(!$service->getWsdlTemplate()){
                throw new \Exception("For regenerate a wsdl file, a configured template is required");
            }
            $this->generateWsdlFile($service->getWsdlTemplate(), $service->getWsdlFile());
        }


        $requestMethod = $_SERVER['REQUEST_METHOD'];

        $soapServer = new \SoapServer($service->getWsdlFile(), array(
            'uri' => $service->getUri(),
            'soap_version' => $service->getSoapVersion(),
            'encoding' => $service->getEncoding(),
            'exceptions' => true,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'send_errors' => $service->getSendError(),
            'classmap' => $service->getClassMap()
        ));

        if ($requestMethod == "GET") {
            return $this->handleWsdl($soapServer);
        } elseif ($requestMethod == "POST") {
            return $this->handleAction($soapServer, $service);
        } else {
            die();
        }
    }

    private function generateWsdlFile(string $wsdlTemplatePath, string $wsdlFilePath)
    {
        if(!$wsdl = file_get_contents($wsdlTemplatePath)){
            throw new \Exception("To generate the wsdl file a template is required");
        }
        $endPoint = $this->uri->getScheme() . "://" . $this->uri->getHost() . $this->uri->getPath();
        $wsdl = str_replace('$endPoint$', $endPoint, $wsdl);
        file_put_contents($wsdlFilePath, $wsdl);
    }

    private function handleWsdl(\SoapServer $soapServer)
    {
        if (strtoupper($this->uri->getQuery()) == "WSDL") {
            $soapServer->handle();
            die();
        } else {
            die();
        }
    }

    private function handleAction(\SoapServer $soapServer, ServiceAbstract $service)
    {
        // Do handler
        try {
            $request = file_get_contents('php://input');
            $service->setRequest($request);

            $soapServer->setObject($service->getClass());

            // Remove the mustUnderstand
            $decodedRequest = preg_replace('/ ([-\w]+\:)?(mustUnderstand=(\'|"))(1|true)(\'|")/', '', $request);

            ob_start();
            $soapServer->handle($decodedRequest);
            $_response = ob_get_clean();
            ob_flush();
            return $this->sendResponse($_response, $service);
        } catch (\Exception $e) {
            ob_flush();
            return $this->sendSoapFault('500', $e->getMessage(), $service);
        }
    }

    private function sendResponse(
        $response,
        ServiceAbstract $service
    ) {
        $service->setResponse($response);

        return new Response($service->getResponse(), Response::HTTP_OK, array(
            'Content-Type' => 'text/xml;charset=utf-8',
            'Content-Length' => strlen($service->getResponse())
        ));
    }

    private function getSoapFault($code, $message)
    {
        return '<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/">
   <SOAP-ENV:Body>
      <SOAP-ENV:Fault>
         <faultcode>' . $code . '</faultcode>
         <faultstring>' . $message . '</faultstring>
      </SOAP-ENV:Fault>
   </SOAP-ENV:Body>
</SOAP-ENV:Envelope>';
    }

    private function sendSoapFault(
        $code,
        $message,
        ServiceAbstract $service
    ) {
        $soapFaultMessage = $this->getSoapFault($code, $message);
        $service->setResponse($soapFaultMessage);
        return new Response($service->getResponse(), Response::HTTP_INTERNAL_SERVER_ERROR, array(
            'Content-Type' => 'text/xml;charset=utf-8',
            'Content-Length' => strlen($service->getResponse())
        ));
    }
}