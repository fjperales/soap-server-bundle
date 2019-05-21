<?php


namespace PacoP\SoapServerLibs\Service;


use DOMDocument;
use RobRichards\WsePhp\WSASoap;
use RobRichards\WsePhp\WSSESoap;
use RobRichards\XMLSecLibs\XMLSecurityDSig;
use RobRichards\XMLSecLibs\XMLSecurityKey;

class ServiceWsseCert extends Service
{
    private $privKey;

    private $pubKey;

    private $passKey;

    private $timestamp;

    private $encOut;

    private $encIn;
    public function __construct($privateKey, $publicKey, $passKey, $timestamp = 3600, $encOut = false, $encIn = false)
    {
        $this->privKey = $privateKey;
        $this->pubKey = $publicKey;
        $this->passKey = $passKey;
        $this->timestamp = $timestamp;
        $this->encOut = $encOut;
        $this->encIn = $encIn;
    }

    public function setRequest($request)
    {
        if ($this->encIn) {
            $request = $this->decrypt($request, $this->privKey, $this->passKey);
        }

        return parent::setRequest($request);
    }

    public function setResponse($response)
    {
        if ($this->encOut) {
            $response = $this->encrypt($response, $this->privKey, $this->pubKey, $this->passKey, $this->timestamp);
        } else {
            $response = $this->sign($response, $this->privKey, $this->pubKey, $this->passKey, $this->timestamp);
        }
        return parent::setResponse($response);
    }


    private function sign($message, $privateKey, $publicKey, $passKey, $timestamp = 3600, $actor = null)
    {
        $message = preg_replace('/ ([-\w]+\:)?(mustUnderstand=(\'|"))(1|true)(\'|")/', '', $message);

        $dom = new \DOMDocument();
        $dom->loadXML($message);

        $objWSA = new WSASoap($dom);

        $dom = $objWSA->getDoc();

        $objWSSE = new WSSESoap($dom, true, $actor);
        /* Sign all headers to include signing the WS-Addressing headers */
        $objWSSE->signAllHeaders = true;

        if ($timestamp != 0) {
            $objWSSE->addTimestamp($timestamp);
        }

        /* create new XMLSec Key using RSA SHA-1 and type is private key */
        // $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA1, array(
        // 'type' => 'private'
        // ));
        $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, array(
            'type' => 'private'
        ));

        /* load the private key from file - last arg is bool if key in file (TRUE) or is string (FALSE) */
        $objKey->passphrase = $passKey;
        $objKey->loadKey($privateKey, true);

        /* Sign the message - also signs appropraite WS-Security items */
        $options = array('algorithm'=>XMLSecurityDSig::SHA256);
        $objWSSE->signSoapDoc($objKey,$options);
        /* Add certificate (BinarySecurityToken) to the message and attach pointer to Signature */
        $token = $objWSSE->addBinaryToken(file_get_contents($publicKey));
        $objWSSE->attachTokentoSig($token);
        $messageSigned = $objWSSE->saveXML();

        return $messageSigned;
    }
    public function encrypt($message, $privateKey, $publicKey, $passKey, $timestamp = 3600)
    {
        $dom = new DOMDocument('1.0');
        $dom->loadXML($message);
        $objWSSE = new WSSESoap($dom);

        /* add Timestamp with no expiration timestamp */
        $objWSSE->addTimestamp($timestamp);
        /* create new XMLSec Key using AES256_CBC and type is private key */
        $objKey = new XMLSecurityKey(XMLSecurityKey::RSA_SHA256, array(
            'type' => 'private'
        ));
        /* load the private key from file - last arg is bool if key in file (true) or is string (false) */
        $objKey->passphrase = $passKey;
        $objKey->loadKey($privateKey, true);
        /* Sign the message - also signs appropiate WS-Security items */
        $options = array(
            "insertBefore" => false,
            "algorithm"=>XMLSecurityDSig::SHA256
        );
        $objWSSE->signSoapDoc($objKey, $options);

        /* Add certificate (BinarySecurityToken) to the message */
        $token = $objWSSE->addBinaryToken(file_get_contents($publicKey));

        /* Attach pointer to Signature */
        $objWSSE->attachTokentoSig($token);
        $objKey = new XMLSecurityKey(XMLSecurityKey::AES256_CBC);
        $objKey->generateSessionKey();

        $siteKey = new XMLSecurityKey(XMLSecurityKey::RSA_OAEP_MGF1P, array(
            'type' => 'public'
        ));
        // $siteKey->passphrase=$passKey;
        $siteKey->loadKey($publicKey, true, true);
        $options = array(
            "KeyInfo" => array(
                "X509SubjectKeyIdentifier" => true
            )
        );
        $objWSSE->encryptSoapDoc($siteKey, $objKey, $options);
        $messageEncrypted = $objWSSE->saveXML();

        return $messageEncrypted;
    }

    public function decrypt($message, $key, $passKey = null)
    {
        $dom = new DOMDocument();
        $dom->loadXML($message);

        $options = array(
            "keys" => array(
                "private" => array(
                    "key" => $key,
                    "passphrase" => $passKey,
                    "isFile" => true,
                    "isCert" => false
                )
            )
        );
        $objWSSE = new WSSESoap($dom);

        $objWSSE->decryptSoapDoc($dom, $options);
        $messageDecrypted = $objWSSE->saveXML();

        return $messageDecrypted;
    }


}