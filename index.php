<?php
define('INTERNAL', 1);
define('MENUITEM', 'myportfolio/edusharing');
define('SECTION_PLUGINTYPE', 'artefact');
define('SECTION_PLUGINNAME', 'edusharing');
define('SECTION_PAGE', 'index');

require(dirname(dirname(dirname(__FILE__))) . '/init.php');
require_once __DIR__ . '/lib/Edusharing.php';

define('TITLE','edu-sharing arbeitsplatz');

$edusharing = new Edusharing();

$smarty = smarty();
$smarty->assign('workspaceurl', get_config_plugin('artefact', 'edusharing','repourl').'/components/workspace?ticket=' . $edusharing->getTicket());
$smarty->assign('INLINEJAVASCRIPT', $js);
$smarty->display('artefact:edusharing:index.tpl');
