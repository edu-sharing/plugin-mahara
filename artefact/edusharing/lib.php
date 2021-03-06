<?php
defined('INTERNAL') || die();

require_once('activity.php');
require_once __DIR__ . '/lib/EdusharingObject.php';

class PluginArtefactEdusharing extends PluginArtefact {

    public static function get_artefact_types() {
        return array(
            'edusharing',
        );
    }

    public static function get_block_types() {
        return array('edusharing');
    }

    public static function get_plugin_name() {
        return 'edusharing';
    }

    public static function menu_items() {
        return array(
            array(
                'path' => 'content/edusharing',
                'url' => 'artefact/edusharing/',
                'title' => 'edu-sharing files',
                'weight' => 20,
            ),
        );
    }

        public static function get_event_subscriptions() {

        /*
         * deleteartefact
         * deleteartefacts
         * deleteblockinstance
         *
         *todo
         * check in saveartefact and blockinstancecommit for deleted or changed objects
         *
         * */


        return array(
            (object)array(
                'plugin' => 'edusharing',
                'event' => 'saveartefact',
                'callfunction' => 'edusharing_saveartefact'
                ),
            (object)array(
                'plugin' => 'edusharing',
                'event' => 'blockinstancecommit',
                'callfunction' => 'edusharing_blockinstancecommit'
                ),
            (object)array(
                'plugin' => 'edusharing',
                'event' => 'deleteblockinstance',
                'callfunction' => 'edusharing_deleteblockinstance'
                ),
            (object)array(
                    'plugin' => 'edusharing',
                    'event' => 'deleteartefact',
                    'callfunction' => 'edusharing_deleteartefact'
                ),
            (object)array(
                    'plugin' => 'edusharing',
                    'event' => 'deleteartefacts',
                    'callfunction' => 'edusharing_deleteartefacts'
                )
            );
    }

    public static function edusharing_deleteartefact($event, ArtefactType $data) {
        error_log('edusharing_deleteartefact');
        try {
            EdusharingObject::deleteByInstanceId($data->get('id'));
        } catch(Exception $e) {
            error_log(print_r($e, true));
        }
    }

    public static function edusharing_deleteartefacts($event, $artefacts) {
        error_log('edusharing_deleteartefacts');
        try {
            foreach($artefacts as $id) {
                EdusharingObject::deleteByInstanceId($id);
            }
        } catch(Exception $e) {
            error_log(print_r($e, true));
        }
    }

    public static function edusharing_deleteblockinstance($event, BlockInstance $data) {
        error_log('edusharing_deleteblockinstance');
            try {
                if($data->get('blocktype') === 'edusharing') {
                    $configdata = $data->get('configdata');
                    $eduid = $configdata['eduid'];
                    $edusharingObject = EdusharingObject::load($eduid);
                    $edusharingObject->delete();
                } else {
                    $instanceId = $data->get('id');
                    EdusharingObject::deleteByInstanceId($instanceId);
                }
            } catch(Exception $e) {
                error_log(print_r($e, true));
            }
    }

    public static function edusharing_saveartefact($event, ArtefactType $data) {
        global $USER;
        error_log('edusharing_saveartefact');
        $lock = $USER->get('username').$data->get('id');
        try {
            $description = $data->get('description');
            $description = self::addObjects(@$description, $data->get('id'));
            $data->set('description', $description);
            //avoid recursion
            if(@!$_SESSION[$lock]) {
                $_SESSION[$lock] = true;
                $data->commit();
            }
        } catch(Exception $e) {
            error_log($e->getMessage());
        }
        $_SESSION[$lock] = false;

    }

    public static function edusharing_blockinstancecommit($event, BlockInstance $data) {
        if($data->get('blocktype') === 'edusharing')
            return; // we handle this in PluginBlocktypeEdusharing::instance_config_save()
        global $USER;
        $lock = $USER->get('username').$data->get('id');
        try {
            $configdata = $data->get('configdata');
            $configdata['text'] = self::addObjects(@$configdata['text'], $data->get('id'));
            $data->set('configdata', $configdata);
            //avoid recursion
            if(@!$_SESSION[$lock]) {
                $_SESSION[$lock] = true;
                $data->commit();
            }
        } catch(Exception $e) {
            error_log($e->getMessage());
        }
        $_SESSION[$lock] = false;
    }

    private static function getEduObjects($content) {
        $return = array();
        if(strpos($content, 'edusharingObject') === false)
            return $return;
        preg_match_all('#<div(.*)edusharingObject(.*)>(.*)</div>#Umsi', $content, $matches,PREG_PATTERN_ORDER);
        $matches = implode('',$matches[0]);
        if(empty($matches))
            return $return;
        preg_match_all('#id="(.*)"#Umsi', $matches, $matches2,PREG_PATTERN_ORDER);
        foreach($matches2[1] as $k => $v) {
            $id = str_replace('edusharing_', '', $v);
            $id = str_replace('user_', '', $id);
            $matches2[1][$k] = $id;
        }
        return $matches2[1];
    }



    private static function addObjects($text, $instanceId) {

        global $USER;

        if(strpos($text, 'edusharingObject') === false)
            return $text;

        preg_match_all('#<img(.*)edusharingObject(.*)>#Umsi', $text, $matchesImg,PREG_PATTERN_ORDER);
        preg_match_all('#<a(.*)edusharingObject(.*)>(.*)</a>#Umsi', $text, $matchesSpan,PREG_PATTERN_ORDER);

        $matches = array_merge($matchesImg[0], $matchesSpan[0]);
        if (!empty($matches)) {
            foreach ($matches as $match) {
                $doc = new DOMDocument();
                $doc->loadHTML($match);
                $node = $doc->getElementsByTagName('img')->item(0);
                $type = 'image';
                if (empty($node)) {
                    $node = $doc->getElementsByTagName('a')->item(0);
                    $qs = $node->getAttribute('href');
                    $type = 'text';
                    if (empty($node)) {
                        error_log('error loading node');
                        return false;
                    }
                } else {
                    $qs = $node->getAttribute('src');
                }
                $params = array();
                parse_str(parse_url($qs, PHP_URL_QUERY), $params);
                //newly added object
                if((!in_array('id', $params)) && $params['objectUrl']) {
                    $edusharingObject = new EdusharingObject($instanceId, $params['objectUrl'],  $params['title'],  $params['mimetype'],  $params['version'],  (isset($params['width']))?$params['width']:'',  (isset($params['height']))?$params['height']:'', $params['resourceId']);

                    $eduid = $edusharingObject -> add();
                    $style = '';
                    if($type === 'image')
                        $style .= 'width='.$params['width'].';height:'.$params['height'].';maxWidth=100%;';
                    switch($params['alignment']) {
                        case 'inline':
                            $style .= 'display:inline-block;';
                            break;
                        case 'block':
                            $style .= 'display:block;';
                            break;
                        case 'left':
                            $style .= 'float:left;';
                            break;
                        case 'right':
                            $style .= 'float:right;';
                            break;
                    }

                    $content = '<a>'.$params['title'].'</a>';
                    if($type === 'image')
                        $content = '<img style="'.$params['width'].'px" src="'.get_config('wwwroot').'/artefact/edusharing/lib/previewHelper.php?sesskey='.$USER->get('sesskey').'&id='.$eduid.'" alt="'.$params['title'].'" title="'.$params['title'].'">';

                    $object = '<div id="edusharing_'.$eduid.'" class="edusharingObject edusharingObjectRaw" style="'.$style.'">'.$content.'</div>';

                    $text = str_replace($match, $object, $text);
                }
            }
        }
        return $text;
    }
}


class ArtefactTypeEdusharing extends ArtefactType {
    public function render_self($options) {
        return 'eduartefactyeah';
    }

    public static function has_config() {
        return true;
    }

    public static function get_config_options() {

        $javascript = <<<EOF
<script>function fetchData(url) {
    url = url+encodeURI(jQuery('#homerepo').val());
    $.get(url, function(data) {
        data = JSON.parse(data);
        if(data.error) {
            alert(data.error);
            return;
        }
        jQuery('[name="repourl"]').val(data.usagewebservice.replace('services/usage2', ''));
        jQuery('[name="repoid"]').val(data.appid);
        jQuery('[name="repopublic"]').val(data.public_key);
        jQuery('[name="appid"]').val('mahara_'+location.hostname);
    });
}</script>
EOF;
        $proxyUrl = get_config('wwwroot').'/artefact/edusharing/lib/importMetadata.php?url=';
        $defaultPublic = get_config_plugin('artefact', 'edusharing', 'apppublic');
        $defaultPrivate = get_config_plugin('artefact', 'edusharing', 'appprivate');
        if(empty($defaultPublic) || empty($defaultPrivate)) {
            require_once __DIR__ . '/lib/Edusharing.php';
            $edusharing = new Edusharing();
            $keypair = $edusharing->getSSlKeys();
            $defaultPublic = $keypair->apppublic;
            $defaultPrivate = $keypair->appprivate;
        }

        $elements =  array(
            'prefill' => array(
                'type'         => 'fieldset',
                'collapsible'  => true,
                'collapsed'    => false,
                 'class'        => 'first',
                'legend'       => 'prefill',
                'elements'     => array(
                    'fetchdata' => array(
                        'type'         => 'html',
                        'title'        => 'repository metadata url',
                        'class' => 'eduprefill',
                        'description'  => 'daten abholen',
                        'value' => $javascript . '<input id="homerepo" placeholder="https://............../metadata?format=lms" class="form-control text autofocus" size="255" name="fetchData"><a onclick="fetchData(\''.$proxyUrl.'\')" class="btn-primary submit btn" style="margin-left: 10px;">Prefill fields</a>'
                    )
                )
            ),
            'repo' => array(
                'type'         => 'fieldset',
                'collapsible'  => true,
                'collapsed'    => false,
               // 'class'        => 'first last',
                'legend'       => 'home repository',
                'elements'     => array(
                    'repourl' => array(
                        'type'         => 'text',
                        'size'         => 255,
                        'title'        => 'repourl',
                        'description'  => 'beschreibung',
                        'rules'        => array(),//array('integer' => true, 'minvalue' => 16, 'maxvalue' => 1600),
                        'defaultvalue' => get_config_plugin('artefact', 'edusharing', 'repourl')
                    ),
                    'repoauthkey' => array(
                        'type'         => 'text',
                        'size'         => 255,
                        'title'        => 'repoauthkey',
                        'description'  => 'repoauthkey',
                        'defaultvalue' => (get_config_plugin('artefact', 'edusharing', 'repoauthkey'))?get_config_plugin('artefact', 'edusharing', 'repoauthkey'):'userid'
                    ),
                    'repoid' => array(
                        'type'         => 'text',
                        'size'         => 255,
                        'title'        => 'repoid',
                        'description'  => 'repoid',
                        'defaultvalue' => get_config_plugin('artefact', 'edusharing', 'repoid')
                    ),
                    'repopublic' => array(
                        'type'  => 'textarea',
                        'rows' => 10,
                        'cols' => 50,
                        'resizable' => false,
                        'title' => 'repopublic',
                        'defaultvalue' => get_config_plugin('artefact', 'edusharing', 'repopublic')
                    )
                )
            ),
            'app' => array(
                'type'         => 'fieldset',
                'collapsible'  => true,
                'collapsed'    => false,
                 'class'        => 'last',
                'legend'       => 'app',
                'elements'     => array(
                    'appid' => array(
                        'type'         => 'text',
                        'size'         => 255,
                        'title'        => 'appid',
                        'description'  => 'appid',
                        'defaultvalue' => get_config_plugin('artefact', 'edusharing', 'appid')
                    ),
                    'appprivate' => array(
                        'type'  => 'textarea',
                        'rows' => 10,
                        'cols' => 100,
                        'resizable' => false,
                        'title' => 'appprivate',
                        'defaultvalue' => $defaultPrivate
                    ),
                    'apppublic' => array(
                        'type'  => 'textarea',
                        'rows' => 10,
                        'cols' => 50,
                        'resizable' => false,
                        'title' => 'apppublic',
                        'defaultvalue' => $defaultPublic
                    ),
                    'apphost' => array(
                        'type'         => 'text',
                        'size'         => 255,
                        'title'        => 'apphost',
                        'description'  => 'apphost',
                        'defaultvalue' => get_config_plugin('artefact', 'edusharing', 'apphost')
                    ),
                    'appdomain' => array(
                        'type'         => 'text',
                        'size'         => 255,
                        'title'        => 'appdomain',
                        'description'  => 'appdomain',
                        'defaultvalue' => get_config_plugin('artefact', 'edusharing', 'appdomain')
                    )
                )
            )
        );

        return array(
            'elements' => $elements,
        );
    }

    public static function save_config_options(Pieform $form, $values) {
        foreach ($values as $key => $value) {
            set_config_plugin('artefact', 'edusharing', $key, $value);
        }
    }

    public static function get_icon($options=null) {}

    public static function is_singular() {
        return false;
    }

    public static function get_links($id) {}
}
