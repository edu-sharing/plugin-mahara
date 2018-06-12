<?php


class Edusharing {

    public function __construct() {

    }

    /*public function getContenturl($eduObj, $displayMode = 'inline') {
        $contenturl = get_config_plugin('artefact', 'edusharing', 'repourl') . '/renderingproxy';
        $contenturl .= '?app_id=' . urlencode ( get_config_plugin('artefact', 'edusharing', 'appid') );
        $contenturl .= '&rep_id=' . get_config_plugin('artefact', 'edusharing', 'repoid');
        $contenturl .= '&obj_id=' . $eduObj['nodeid'];
        $contenturl .= '&resource_id=' . urlencode ( $eduObj['uid'] );
        $contenturl .= '&course_id=' . urlencode ( $eduObj['contentid']);
        $contenturl .= '&display=' . $displayMode;
        if($displayMode === 'window')
            $contenturl .= '&closeOnBack=true';
        $contenturl .= '&width=' . $_GET['edusharing_width'];
        $contenturl .= '&height='  . $_GET['edusharing_height'];
        $contenturl .= '&language=' . 'de';
        $contenturl .= '&version=' . $eduObj['version'];
        $contenturl .= $this -> getSecurityParams();

        return $contenturl;
    }

    public function getSecurityParams() {
        ///////change username!!!!!!!!
        $paramString = '';
        $ts = round ( microtime ( true ) * 1000 );
        $paramString .= '&ts=' . $ts;
        $paramString .= '&u=' . urlencode( 'sp4DWsQVmJg=' ); //es_guest
        $signature = '';
        $priv_key = get_config_plugin('artefact', 'edusharing', 'appprivate');
        $pkeyid = openssl_get_privatekey ( $priv_key );
        openssl_sign ( get_config_plugin('artefact', 'edusharing', 'appid') . $ts, $signature, $pkeyid );
        $signature = base64_encode ( $signature );
        openssl_free_key ( $pkeyid );
        $paramString .= '&sig=' . urlencode ( $signature );
        $paramString .= '&signed=' . urlencode(get_config_plugin('artefact', 'edusharing', 'appid').$ts);
        $paramString .= '&ticket=' . urlencode(base64_encode($this->encrypt_with_repo_public($this->getTicket())));

        return $paramString;
    }*/

    public function getTicket() {
        global $USER;
        $username = $USER->get('username');
        try {
            $eduSoapClient = new EduSoapClient(get_config_plugin('artefact', 'edusharing', 'repourl') . '/services/authbyapp?wsdl');
            if (isset($_SESSION["repository_ticket"])) {
                $params = array(get_config_plugin('artefact', 'edusharing', 'repoauthkey') => $username, "ticket" => $_SESSION["repository_ticket"]);
                try {
                    $alfReturn = $eduSoapClient -> checkTicket($params);

                    if ($alfReturn === true) {

                        return $_SESSION["repository_ticket"];
                    }
                } catch (Exception $e) {
                    return $e;
                }
            }
            $paramsTrusted = array("applicationId" => get_config_plugin('artefact', 'edusharing', 'appid'), "ticket" => session_id(), "ssoData" => array(array('key' => get_config_plugin('artefact', 'edusharing', 'repoauthkey'),'value' => $username)));
            $alfReturn = $eduSoapClient -> authenticateByTrustedApp($paramsTrusted);
            $ticket = $alfReturn -> authenticateByTrustedAppReturn -> ticket;
            $_SESSION["repository_ticket"] = $ticket;
            return $ticket;
        } catch (Exception $e) {
            error_log('Error getting ticket in ' . get_class($this));
            return;
        }
    }

    private function encrypt_with_repo_public($data) {
        $crypted = '';
        $key = openssl_get_publickey(get_config_plugin('artefact', 'edusharing', 'repopublic'));
        openssl_public_encrypt($data ,$crypted, $key);
        if($crypted === false) {
            error_log (  'Encryption error' );
            return false;
        }
        return $crypted;
    }

    public function getSSlKeys() {
        $res = openssl_pkey_new();
        openssl_pkey_export($res, $privatekey);
        $publickey = openssl_pkey_get_details($res);
        return (object)array('appprivate'=>$privatekey, 'apppublic'=>$publickey["key"]);
    }

}