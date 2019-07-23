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
        return array('external' => 33000);
    }

     /**
     * Optional method. If exists, allows this class to decide the title for
     * all blockinstances of this type
     */
    public static function get_instance_title(BlockInstance $bi) {
        return 'edusharing';

    }

    public static function render_instance(BlockInstance $instance, $editing=false, $versioning = false) {
        $return = '';
        $configdata = $instance->get('configdata');
        if(isset($configdata['eduid'])) {
            $return = '<div class="edusharingObject edusharingObjectRaw" id="edusharing_'.$configdata['eduid'].'"></div>';
        }
        return $return;
    }

    public static function has_instance_config() {
        return true;
    }

    public static function instance_config_form(BlockInstance $instance) {

        global $USER;

        $configdata = $instance->get('configdata');
        $instance->set('artefactplugin', 'edusharing');

        $edusharing = new Edusharing();
        $ticket = $edusharing -> getTicket();

        $form = array();

        //handle get condition
        $form['eduversionshow'] = array(
            'type'    => 'radio',
            'title'   => get_string('eduversionshow', 'blocktype.edusharing/edusharing'),
            'class' =>  (isset($configdata['eduobjectUrl']))?'hidden':'',
            'options' => array(0=>get_string('eduversionshow_last', 'blocktype.edusharing/edusharing'), 1=>get_string('eduversionshow_current', 'blocktype.edusharing/edusharing')),
            'defaultvalue' => 0,
        );
        $form['eduobjectUrl'] = array(
            'type' => 'text',
            'class' => 'hidden',
            'defaultvalue' => (isset($configdata['eduobjectUrl']))?$configdata['eduobjectUrl']:'',
        );
        $form['eduversion'] = array(
            'type' => 'text',
            'class' => 'hidden',
            'defaultvalue' => (isset($configdata['eduversion']))?$configdata['eduversion']:'',
        );
        $form['previewurl'] = array(
            'type' => 'text',
            'class' => 'hidden',
            'defaultvalue' => (isset($configdata['previewurl']))?$configdata['previewurl'] . '&sesskey=' . $USER->get('sesskey'):'',
        );
        $form['edusearchurl'] = array(
            'type' => 'hidden',
            'value' => get_config_plugin('artefact', 'edusharing', 'repourl').'/components/search?applyDirectories=true&reurl=IFRAME&ticket='.$ticket,
        );
        $form['edumimetype'] = array(
            'type' => 'text',
            'class' => 'hidden',
            'defaultvalue' => (isset($configdata['edumimetype']))?$configdata['edumimetype']:'',
        );
        $form['eduid'] = array(
            'type' => 'text',
            'class' => 'hidden',
            'defaultvalue' => (isset($configdata['eduid']))?$configdata['eduid']:'',
        );
        $form['eduwidth'] = array(
            'type' => 'text',
            'class' => 'hidden',
            'defaultvalue' => (isset($configdata['eduwidth']))?$configdata['eduwidth']:'',
        );
        $form['eduheight'] = array(
            'type' => 'text',
            'class' => 'hidden',
            'defaultvalue' => (isset($configdata['eduheight']))?$configdata['eduheight']:'',
        );
        $form['eduresourceid'] = array(
            'type' => 'text',
            'class' => 'hidden',
            'defaultvalue' => (isset($configdata['eduresourceid']))?$configdata['eduresourceid']:'',
        );
        return $form;
    }

    public static function instance_config_save($values, $instance) {
        try {
            $edusharingObject = new EdusharingObject($instance->get('id'), $values['eduobjectUrl'], $values['title'], $values['edumimetype'], ($values['eduversionshow'] == 0) ? -1 : $values['eduversion'], $values['eduwidth'], $values['eduheight'], $values['eduresourceid']);
            if (!empty($values['eduid'])) {
                $edusharingObject->delete();
            }
            $values['eduid'] = $edusharingObject->add();
            if($values['eduid'] === false)
                throw new Exception('EduSharingObject::add() failed');

            $values['previewurl'] = get_config('wwwroot').'/artefact/edusharing/lib/previewHelper.php?id=' . $values['eduid'];
            //repo deos not support preview versions, set it anyway
            if($values['eduversionshow'] > 0) {
                $values['previewurl'] .= '&version=' . $values['eduversion'];
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
        }
        return $values;
    }

    public static function get_instance_config_javascript(BlockInstance $instance) {
        return array(
            array(
                'file'   => 'js/blocktype.js',
                // 'initjs' => "addNewPostShortcut($blockid);",
            )
        );
    }

    public static function allowed_in_view(View $view) {
        return true;// $view->get('owner') != null;
    }
}
