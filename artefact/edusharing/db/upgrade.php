<?php
/**
 *
 *
 */

defined('INTERNAL') || die();

function xmldb_artefact_edusharing_upgrade($oldversion=0) {

    global $DB;

    $dbman = $DB->get_manager(); // loads ddl manager and xmldb classes

    $status = true;

    if ($status && $oldversion < 2019071800) {

        try {
            $xmldbtable = new xmldb_table('artefact_edusharing');
            $xmldbfield = new xmldb_field(
                'resourceid',
                XMLDB_TYPE_INTEGER,
                '10',
                null,
                false,
                false,
                null,
                'height'
            );
            $dbman->add_field($xmldbtable, $xmldbfield);
        } catch (Exception $e) {
            trigger_error($e->getMessage(), E_USER_WARNING);
            error_log($e->getMessage());
        }
    }

    return $status;
}
