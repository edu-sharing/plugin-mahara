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

if(isset($_GET['childobject_order']))
    $childobject_order = $_GET['childobject_order'];
else
    $childobject_order = -1;

$edusharingObject = EdusharingObject::load($_GET['id']);
$edusharing = new Edusharing();
$redirecturl = $edusharing->getRedirectUrl($edusharingObject);
$ts = $timestamp = round(microtime(true) * 1000);
$redirecturl .= '&ts=' . $ts;
$objId = str_replace('ccrep://'.get_config_plugin('artefact', 'edusharing', 'repoid').'/', '', $edusharingObject->objecturl);
$data = get_config_plugin('artefact', 'edusharing', 'appid') . $ts . $objId;
$redirecturl .= '&sig=' . urlencode($edusharing->getSignature($data));
$redirecturl .= '&signed=' . urlencode($data);
$redirecturl .= '&closeOnBack=true';
$redirecturl .= '&ticket=' . urlencode(base64_encode($edusharing->encryptWithRepoPublic($edusharing->getTicket())));
$redirecturl .= '&childobject_order=' . $childobject_order;
redirect($redirecturl);
