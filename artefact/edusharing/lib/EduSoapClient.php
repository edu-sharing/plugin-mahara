<?php
class EduSoapClient extends \SoapClient {

    public function __construct($wsdl, $options = array()) {
        parent::__construct($wsdl, $options);
        $this -> setSoapHeaders();
    }

    private function setSoapHeaders() {
        try {
            $timestamp = round(microtime(true) * 1000);
            $signData = get_config_plugin('artefact', 'edusharing', 'appid') . $timestamp;
            $priv_key = get_config_plugin('artefact', 'edusharing', 'appprivate');
            $pkeyid = openssl_get_privatekey($priv_key);
            openssl_sign($signData, $signature, $pkeyid);
            $signature = base64_encode($signature);
            openssl_free_key($pkeyid);
            $headers = array();
            $headers[] = new \SOAPHeader('http://webservices.edu_sharing.org', 'appId', get_config_plugin('artefact', 'edusharing', 'appid'));
            $headers[] = new \SOAPHeader('http://webservices.edu_sharing.org', 'timestamp', $timestamp);
            $headers[] = new \SOAPHeader('http://webservices.edu_sharing.org', 'signature', $signature);
            $headers[] = new \SOAPHeader('http://webservices.edu_sharing.org', 'signed', $signData);
            parent::__setSoapHeaders($headers);
        } catch (\Exception $e) {
            throw new \Exception('Could not set soap headers - ' . $e -> getMessage());
        }
    }

}