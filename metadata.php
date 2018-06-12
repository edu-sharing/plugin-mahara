<?php

define('INTERNAL', 1);
define('PUBLIC', 1);
require(__DIR__. '/../../init.php');

$xml = new SimpleXMLElement(
    '<?xml version="1.0" encoding="utf-8" ?><!DOCTYPE properties SYSTEM "http://java.sun.com/dtd/properties.dtd"><properties></properties>');

$entry = $xml->addChild('entry', get_config_plugin('artefact', 'edusharing', 'appid'));
$entry->addAttribute('key', 'appid');

$entry = $xml->addChild('entry', get_config_plugin('artefact', 'edusharing', 'apppublic'));
$entry->addAttribute('key', 'public_key');

$entry = $xml->addChild('entry', 'mahara');
$entry->addAttribute('key', 'appcaption');

$entry = $xml->addChild('entry', 'true');
$entry->addAttribute('key', 'trustedclient');

$entry = $xml->addChild('entry', get_config_plugin('artefact', 'edusharing', 'appdomain'));
$entry->addAttribute('key', 'domain');

$entry = $xml->addChild('entry', get_config_plugin('artefact', 'edusharing', 'apphost'));
$entry->addAttribute('key', 'host');

$entry = $xml->addChild('entry', 'mahara');
$entry->addAttribute('key', 'subtype');

$entry = $xml->addChild('entry', 'lms');
$entry->addAttribute('key', 'type');

$entry = $xml->addChild('entry', 'moodle:course/update');
$entry->addAttribute('key', 'hasTeachingPermission');

header('Content-type: text/xml');
print(html_entity_decode($xml->asXML()));
exit();
