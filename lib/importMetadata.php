<?php
$metadataurl = $_GET['url'];
if(empty($metadataurl)) {
    echo json_encode(array('error'=>'Repository metadata url is empty'));
    exit();
}
try {
    $return = array();
    $xml = new DOMDocument();
    libxml_use_internal_errors(true);
    if (@$xml->load($metadataurl) === false) {
        echo json_encode(array('error'=>'Cannot load XML'));
        exit();
    }
    $xml->preserveWhiteSpace = false;
    $xml->formatOutput = true;
    $entrys = $xml->getElementsByTagName('entry');
    foreach ($entrys as $entry) {
        $return[$entry->getAttribute('key')] = $entry->nodeValue;
    }
    if(empty($return['appid']))
        echo json_encode(array('error'=>'Invalid Data received'));
    else
        echo json_encode($return);
    exit();
} catch (Exception $e) {
    echo json_encode(array('error'=>$e->getMessage()));
    exit();
}

