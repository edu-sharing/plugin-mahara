<?php
define('INTERNAL', 1);
define('MENUITEM', 'myportfolio/myplugin');
define('SECTION_PLUGINTYPE', 'artefact');
define('SECTION_PLUGINNAME', 'edusharing');
define('SECTION_PAGE', 'index');

require(dirname(dirname(dirname(FILE))) . '/init.php');
define('TITLE', get_string('MenuItemString', 'artefact.edusharing'));

$indexstring = get_string('IndexPageString', 'artefact.edusharing');

$smarty = smarty();
$smarty->assign('indexstring', $indexstring);
$smarty->display('artefact:edusharing:index.tpl');
