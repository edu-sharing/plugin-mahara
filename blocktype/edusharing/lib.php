<?php

defined('INTERNAL') || die();

require_once get_config('docroot') . '/artefact/edusharing/lib/Edusharing.php';
require_once get_config('docroot') . '/artefact/edusharing/lib/EduSoapClient.php';
require_once get_config('docroot') . '/artefact/edusharing/lib/EdusharingObject.php';


class PluginBlocktypeEdusharing extends MaharaCoreBlocktype {

    public static function get_title() {
        return 'edusharing';//get_string('title', 'blocktype.edusharing/edusharing');
    }

    public static function get_description() {
        return 'ediscription';//get_string('description1', 'blocktype.edusharing/edusharing');
    }

    public static function get_categories() {
        return array('general' => 1500);
    }

     /**
     * Optional method. If exists, allows this class to decide the title for
     * all blockinstances of this type
     */
    public static function get_instance_title(BlockInstance $bi) {
        return 'edusharing';
    }

    public static function render_instance(BlockInstance $instance, $editing=false) {
        $return = '<div class="edusharingObject"';
        foreach($instance->get('configdata') as $key => $value) {
            $return .= 'data-' . $key . '="'.$value.'"';
        }
        $return .= '>'.var_dump($instance).'</div>';
        return $return;
    }

    public static function has_instance_config() {
        return true;
    }

    public static function instance_config_form(BlockInstance $instance) {
        $configdata = $instance->get('configdata');
        $instance->set('artefactplugin', 'edusharing');

        $edusharing = new Edusharing();
        $ticket = $edusharing -> getTicket();

        $form = array();

        $form['eduversionshow'] = array(
            'type'    => 'radio',
            'title'   => 'eduversionshow',
            'options' => array(0=>'immer aktuelle', 1=>'genau diese'),
            'defaultvalue' => 0,
        );
        $form['eduobjectUrl'] = array(
            'type' => 'text',
            'class' => 'hidden',
            'defaultvalue' => (isset($configdata['eduobjectUrl'])?$configdata['eduobjectUrl']:''),
        );
        if(isset($configdata['eduobjectUrl'])) {
            $form['formereduobjectUrl'] = array(
                'type' => 'text',
                'class' => 'hidden',
                'value' => $configdata['eduobjectUrl'],
            );
        }
        $form['eduversion'] = array(
            'type' => 'text',
            'class' => 'hidden',
            'defaultvalue' => (isset($configdata['eduversion'])?$configdata['eduversion']:''),
        );
        $form['edusearchurl'] = array(
            'type' => 'hidden',
            'value' => get_config_plugin('artefact', 'edusharing', 'repourl').'/components/search?reurl=IFRAME&ticket='.$ticket,
        );

        return $form;
    }

    public static function instance_config_save($values) {
        if($values['formereduobjectUrl']) {
            $edusharingObject = new EdusharingObject($values['formereduobjectUrl'], 0, '', '', ($values['eduversionshow']==0)?-1:$values['eduversion']);
            $edusharingObject->deleteUsage();
        }
        $edusharingObject = new EdusharingObject($values['eduobjectUrl'], 0, '', '', ($values['eduversionshow']==0)?-1:$values['eduversion']);
        $edusharingObject -> setUsage();
        return $values;
    }

    //obviously this is called when cnofiguring instance
    public static function get_instance_config_javascript(BlockInstance $instance) {
        return array(
            array(
                'file'   => 'js/blocktype.js',
                // 'initjs' => "addNewPostShortcut($blockid);",
            )
        );
    }



    public static function get_event_subscriptions() {
        $sub = new stdClass();
        $sub->event = 'deleteblockinstance';
        $sub->callfunction = 'testy';
        return array($sub);
    }

    public static function testy($event, $user) {
        echo 'testy';
        var_dump($event);
    }


/*
     *
     * hierein könnte man das filter js rein machen wenn es so funktionieren soll
 *
 * inline javascript kann man aber auch reinhauen in der render methode
     *
     **/
    public static function get_instance_javascript(BlockInstance $instance) {
        return array(
            array(
                'file'   => 'js/render.js',
               // 'initjs' => "addNewPostShortcut($blockid);",
            )
        );
    }

    public static function allowed_in_view(View $view) {
        return true;// $view->get('owner') != null;
    }
}
