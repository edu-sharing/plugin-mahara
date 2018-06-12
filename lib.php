<?php
defined('INTERNAL') || die();

require_once('activity.php');

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
                'path' => 'myportfolio/edusharing',
                'url' => 'artefact/edusharing/',
                'title' => get_string('MenuItemString', 'artefact.edusharing'),
                'weight' => 20,
            ),
        );
    }
    }


class ArtefactTypeEdusharing extends ArtefactType {
    public function render_self($options) {
        return get_string('TestName', 'artefact.edusharing');
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
        //console.log(data);
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
            require_once __DIR__ . 'lib/Edusharing.php';
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
                // 'class'        => 'first last',
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
                  /*  'apphost' => array(
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
            ),*/)
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