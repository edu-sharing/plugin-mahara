<?php

require_once __DIR__ . '/EduSoapClient.php';

class EdusharingObject
{

    public $id;
    public $instanceId;
    public $objecturl;
    public $title;
    public $mimetype;
    public $version;
    public $width;
    public $height;

    public function __construct($instanceId = 0, $objecturl = '', $title = '', $mimetype = '', $version = '', $width = null, $height = null) {
        $this->instanceId = (int)$instanceId;
        $this->objecturl = $objecturl;
        $this->title = $title;
        $this->mimetype = $mimetype;
        $this->version = $version;
        $this->width = $width;
        $this->height = $height;
    }

    public function getPreviewUrl() {
        return 'https://www.gstatic.com/webp/gallery/4.sm.jpg?a=b';
    }

    public function deleteUsage() {
        $eduSoapClient = new EduSoapClient(get_config_plugin('artefact', 'edusharing', 'repourl') . '/services/usage2?wsdl');
        $params = array(
            "eduRef" => $this->objecturl,
            "user" => null,
            "lmsId" => get_config_plugin('artefact', 'edusharing', 'appid'),
            "courseId" => $this->instanceId,
            "resourceId" => $this->instanceId
        );
        try {
            $eduSoapClient->deleteUsage($params);
        } catch (\SoapFault $e) {
            throw new \Exception($e->faultstring);
        }
        return true;
    }

    public function setUsage() {
        global $USER;
        $eduSoapClient = new EduSoapClient(get_config_plugin('artefact', 'edusharing', 'repourl') . '/services/usage2?wsdl');
        $params = array(
            "eduRef" => $this->objecturl,
            "user" => $USER->get('username'),
            "lmsId" => get_config_plugin('artefact', 'edusharing', 'appid'),
            "courseId" => $this->instanceId,
            "userMail" => $USER->get('email'),
            "fromUsed" => '2002-05-30T09:00:00',
            "toUsed" => '2222-05-30T09:00:00',
            "distinctPersons" => '0',
            "version" => $this->version,
            "resourceId" => $this->instanceId,
            "xmlParams" => ''
        );
        try {
            $eduSoapClient->setUsage($params);
        } catch (\SoapFault $e) {
            throw new \Exception($e->faultstring);
        }
        return true;
    }

    public function add() {
        $eduid = $this -> dbInsert();
        $this -> setUsage();
        return $eduid;
    }

    public function delete() {
        $this->deleteUsage();
        $this->dbDelete();
    }

    public static function load($eduid) {
        $record = get_record('artefact_edusharing', 'id', $eduid);
        return new EdusharingObject($record->instanceid,$record->objecturl,$record->title,$record->mimetype,$record->version, $record->width, $record->height);
    }

    private function dbDelete() {
        return delete_records('artefact_edusharing','instanceid', $this->instanceId,'objecturl',$this->objecturl);
    }


    private function dbInsert() {
        return insert_record('artefact_edusharing', (object)array('instanceid' => $this->instanceId,'objecturl' => $this->objecturl,'title' => $this->title,'mimetype' => $this->mimetype,'version' => $this->version, 'width'=>$this->width, 'height'=>$this->height), 'id', true);
    }

    public static function deleteByInstanceId($instanceId) {
        $records = get_records_array('artefact_edusharing', 'instanceid', $instanceId);
        if(is_array($records)) {
            foreach($records as $record) {
                $eduSharingObject = new EdusharingObject($record->instanceid,$record->objecturl,$record->title,$record->mimetype,$record->version, $record->width, $record->height);
                $eduSharingObject -> delete();
            }
        }
    }
}
