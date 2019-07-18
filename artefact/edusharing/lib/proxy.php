<?php
define('INTERNAL', 1);
require(__DIR__. '/../../../init.php');
require_once (__DIR__ . '/Edusharing.php');
require_once (__DIR__ . '/EdusharingObject.php');

/**
 * Class for ajax based rendering
 *
 * @copyright metaVentis GmbH — http://metaventis.com
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class filter_edusharing_edurender {

    /**
     * Get rendered object via curl
     *
     * @param string $url
     * @return string
     * @throws Exception
     */
    public function filter_edusharing_get_render_html($url) {
        $inline = "";
        try {
            $curlhandle = curl_init($url);
            curl_setopt($curlhandle, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($curlhandle, CURLOPT_HEADER, 0);
            // DO NOT RETURN HTTP HEADERS
            curl_setopt($curlhandle, CURLOPT_RETURNTRANSFER, 1);
            // RETURN THE CONTENTS OF THE CALL
            curl_setopt($curlhandle, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($curlhandle, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curlhandle, CURLOPT_SSL_VERIFYHOST, false);
            $inline = curl_exec($curlhandle);
            if($inline === false) {
                trigger_error(curl_error($curlhandle), E_USER_WARNING);
            }
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_WARNING);
        }
        curl_close($curlhandle);
        return $inline;
    }

    /**
     * Prepare rendered object for display
     *
     * @param string $html
     */
    public function filter_edusharing_display($html, $id, $title) {
        global $USER;
        error_reporting(0);
        $html = str_replace(array("\n", "\r", "\n"), '', $html);
       /*
         * replaces {{{LMS_INLINE_HELPER_SCRIPT}}}
         */
        $html = str_replace("{{{LMS_INLINE_HELPER_SCRIPT}}}", get_config('wwwroot') . "/artefact/edusharing/lib/inlineHelper.php?sesskey=".$USER->get('sesskey')."&id=" . $id, $html);
        $html = preg_replace("/<es:title[^>]*>.*<\/es:title>/Uims", utf8_decode($title), $html);
        echo $html;
        exit();
    }
}


global $USER;

if($_GET['sesskey'] !== $USER->get('sesskey')) {
    echo 'invalid sesskey';
    exit();
}
if(empty($_GET['id'])) {
    echo 'id is empty';
    exit();
}

$id = str_replace(array('edusharing_', 'user_'), '', $_GET['id']);
$username = $USER->get('username');
$edusharing = new Edusharing();
$edusharingObject = EdusharingObject::load($id);

$objId = str_replace('ccrep://'.get_config_plugin('artefact', 'edusharing', 'repoid').'/', '', $edusharingObject->objecturl);
$ts = $timestamp = round(microtime(true) * 1000);
$url = get_config_plugin('artefact', 'edusharing', 'repourl') . '/renderingproxy';
$url .= '?ts=' . $ts;
$dataToSign = get_config_plugin('artefact', 'edusharing', 'appid') . $ts . $objId;
$url .= '&sig=' . urlencode($edusharing->getSignature($dataToSign));
$url .= '&signed=' . urlencode($dataToSign);
$url .= '&videoFormat=' . $_GET['videoFormat'];
$url .= '&rep_id=' . get_config_plugin('artefact', 'edusharing', 'repoid');
$url .= '&app_id=' . get_config_plugin('artefact', 'edusharing', 'appid');
$url .= '&u='. rawurlencode(base64_encode($edusharing->encryptWithRepoPublic($username)));
$url .= '&obj_id=' . $objId;
$url .= '&resource_id='.urlencode($edusharingObject->resourceId);
$url .= '&course_id='.urlencode($edusharingObject->instanceId);
$url .= '&version='.urlencode($edusharingObject->version); //prüfen
$url .= '&display=inline';
$url .= '&width=' . urlencode($edusharingObject->width);
$url .= '&height=' . urlencode($edusharingObject->height);
$e = new filter_edusharing_edurender();
$html = $e->filter_edusharing_get_render_html($url);
$e->filter_edusharing_display($html, $id, $edusharingObject->title);
