<?php
require_once __DIR__ . '/EduSoapClient.php';

define('EDUSHARING_DISPLAY_MODE_DISPLAY', 'window');
define('EDUSHARING_DISPLAY_MODE_INLINE', 'inline');

class Edusharing {

    public function __construct() {

    }

    function getSignature($data) {
        $privkey = get_config_plugin('artefact', 'edusharing', 'appprivate');
        $pkeyid = openssl_get_privatekey($privkey);
        openssl_sign($data, $signature, $pkeyid);
        $signature = base64_encode($signature);
        openssl_free_key($pkeyid);
        return $signature;
    }

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

    public function encryptWithRepoPublic($data) {
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

    function getRedirectUrl(EdusharingObject $edusharingObject, $displaymode = EDUSHARING_DISPLAY_MODE_DISPLAY) {
        global $USER;
        $edusharing = new Edusharing();
        $url = get_config_plugin('artefact', 'edusharing', 'repourl') . '/renderingproxy';
        $url .= '?rep_id=' . get_config_plugin('artefact', 'edusharing', 'repoid');
        $url .= '&app_id=' . get_config_plugin('artefact', 'edusharing', 'appid');
        $url .= '&session='.urlencode(session_id());
        $objId = str_replace('ccrep://'.get_config_plugin('artefact', 'edusharing', 'repoid').'/', '', $edusharingObject->objecturl);
        $url .= '&obj_id='.urlencode($objId);
        $url .= '&resource_id='.urlencode($edusharingObject->resourceId);
        $url .= '&course_id='.urlencode($edusharingObject->instanceId);
        $url .= '&display='.urlencode($displaymode);
        $url .= '&width=' . urlencode($edusharingObject->width);
        $url .= '&height=' . urlencode($edusharingObject->height);
        $url .= '&version=' . urlencode($edusharingObject->version);
        $url .= '&locale=' . urlencode(get_user_institution_language($USER->id)); //repository
        $url .= '&language=' . urlencode(get_user_institution_language($USER->id)); //rendering service
        $url .= '&u='. rawurlencode(base64_encode($edusharing->encryptWithRepoPublic($USER->get('username'))));
        return $url;
    }

}