<?php
define('INTERNAL', 1);
require(__DIR__. '/../../../init.php');
require_once (__DIR__ . '/Edusharing.php');
require_once (__DIR__ . '/EdusharingObject.php');

if($_GET['sesskey'] !== $USER->get('sesskey')) {
    echo 'invalid sesskey';
    exit();
}
if(empty($_GET['id'])) {
    echo 'id is empty';
    exit();
}


$edusharingObject = EdusharingObject::load($_GET['id']);
$edusharing = new Edusharing();
$url = get_config_plugin('artefact', 'edusharing', 'repourl') . '/preview';
$ts = $timestamp = round(microtime(true) * 1000);
$url .= '?ts=' . $ts;
$objId = str_replace('ccrep://'.get_config_plugin('artefact', 'edusharing', 'repoid').'/', '', $edusharingObject->objecturl);
$data = get_config_plugin('artefact', 'edusharing', 'appid') . $ts . $objId;
$url .= '&sig=' . urlencode($edusharing->getSignature($data));
$url .= '&signed=' . urlencode($data);
$url .= '&closeOnBack=true';
$url .= '&appId=' . get_config_plugin('artefact', 'edusharing', 'appid');
$url .= '&resourceId='.urlencode($edusharingObject->instanceId);
$url .= '&courseId='.urlencode($edusharingObject->instanceId);
$url .= '&nodeId=' . $objId;
$url .= '&repoId=' . get_config_plugin('artefact', 'edusharing', 'repoid');

$curlhandle = curl_init($url);
curl_setopt($curlhandle, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curlhandle, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($curlhandle, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($curlhandle, CURLOPT_HEADER, 0);
curl_setopt($curlhandle, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curlhandle, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
$output = curl_exec($curlhandle);
$mimetype = curl_getinfo($curlhandle, CURLINFO_CONTENT_TYPE);
curl_close($curlhandle);
header('Content-type: ' . $mimetype);
echo $output;
exit();
