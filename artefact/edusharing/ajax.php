<?php

require_once __DIR__ .'/lib/EdusharingObject.php';

if($_GET['action'] === 'previewUrl') {
    $edusharingObject = new EdusharingObject();
    echo $edusharingObject -> getPreviewUrl();
}
/*
if($_GET['action'] === 'proxyUrl') {
    $edusharingObject = new EdusharingObject();
    echo $edusharingObject -> getPreviewUrl();
}*/