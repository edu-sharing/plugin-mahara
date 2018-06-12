<?php

class EdusharingObject
{

    public $uid;
    public $objecturl;
    public $contentId;
    public $title;
    public $mimetype;
    public $version;
    public $logger;

    public function __construct($objecturl = '', $contentId = 0, $title = '', $mimetype = '', $version = '', $uid = '')
    {
        $this->objecturl = $objecturl;
        $this->contentId = $contentId;
        $this->title = $title;
        $this->mimetype = $mimetype;
        $this->version = $version;
        $this->uid = $uid;

    }

    public function deleteUsage()
    {
        $eduSoapClient = new EduSoapClient(get_config_plugin('artefact', 'edusharing', 'repourl') . '/services/usage2?wsdl');
        $params = array(
            "eduRef" => $this->objecturl,
            "user" => null,
            "lmsId" => get_config_plugin('artefact', 'edusharing', 'appid'),
            "courseId" => $this->contentId,
            "resourceId" => $this->uid
        );
        try {
            $eduSoapClient->deleteUsage($params);
        } catch (\SoapFault $e) {
            throw new \Exception($e->faultstring);
        }
        return true;
    }

    public function setUsage()
    {
        global $USER;
        $eduSoapClient = new EduSoapClient(get_config_plugin('artefact', 'edusharing', 'repourl') . '/services/usage2?wsdl');
        $params = array(
            "eduRef" => $this->objecturl,
            "user" => $USER->get('username'),
            "lmsId" => get_config_plugin('artefact', 'edusharing', 'appid'),
            "courseId" => $this->contentId,
            "userMail" => $USER->get('email'),
            "fromUsed" => '2002-05-30T09:00:00',
            "toUsed" => '2222-05-30T09:00:00',
            "distinctPersons" => '0',
            "version" => $this->version,
            "resourceId" => $this->uid,
            "xmlParams" => ''
        );
        try {
            $eduSoapClient->setUsage($params);
        } catch (\SoapFault $e) {
            throw new \Exception($e->faultstring);
        }
        return true;
    }

    /*
        public function exists() {
            $row = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)
                ->getConnectionForTable('tx_edusharing_object')
                ->select(
                    ['uid'],
                    'tx_edusharing_object',
                    [
                        'objecturl' => $this->objecturl,
                        'contentid' => $this->contentId,
                        'version' => $this->version
                    ]
                )
                ->fetch();

            if($row) {
                $this->uid = $row['uid'];
                return true;
            }
            return false;
        }

        public function add() {
            try {
                $this -> dbInsert();
                $this -> setUsage();
            } catch(\Exception $e) {
                return false;
            }
            return true;
        }

        public function delete() {
            try {
                $this->deleteUsage();
                $this->dbDelete();
            } catch(\Exception $e) {
                return false;
            }
            return true;
        }
    private function dbDelete() {
            $connectionObject  = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)->getConnectionForTable('tx_edusharing_object');
            $uid = $connectionObject->delete(
                'tx_edusharing_object',
                [
                    'uid' => $this->uid
                ]
            );
            return $uid;
        }

        private function dbInsert() {
            $connectionObject  = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class)->getConnectionForTable('tx_edusharing_object');
            $connectionObject->insert(
                'tx_edusharing_object',
                [
                    'objecturl' => $this->objecturl,
                    'contentid' => $this->contentId,
                    'title' => $this->title,
                    'version' => $this->version,
                    'mimetype' => $this->mimetype
                ]
            );

            $this->uid = (int)$connectionObject->lastInsertId('tx_edusharing_object');
            return true;
        }*/
}