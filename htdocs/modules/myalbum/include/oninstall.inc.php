<?php

// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

$moduleDirName = $_SESSION['myalbum_mydirname'];
include_once XOOPS_ROOT_PATH . "/modules/$moduleDirName/language/english/myalbum_constants.php";

eval('
function xoops_module_install_' . $moduleDirName . '( $module )
{
    $modid = $module->getVar("mid") ;
    $gpermHandler = xoops_getHandler("groupperm");

    $global_perms_array = array(
        GPERM_INSERTABLE => _ALBM_GPERM_G_INSERTABLE ,
        GPERM_SUPERINSERT | GPERM_INSERTABLE => _ALBM_GPERM_G_SUPERINSERT ,
//      GPERM_EDITABLE => _ALBM_GPERM_G_EDITABLE ,
        GPERM_SUPEREDIT | GPERM_EDITABLE => _ALBM_GPERM_G_SUPEREDIT ,
//      GPERM_DELETABLE => _ALBM_GPERM_G_DELETABLE ,
        GPERM_SUPERDELETE | GPERM_DELETABLE => _ALBM_GPERM_G_SUPERDELETE ,
        GPERM_RATEVIEW => _ALBM_GPERM_G_RATEVIEW ,
        GPERM_RATEVOTE | GPERM_RATEVIEW => _ALBM_GPERM_G_RATEVOTE
    ) ;

    foreach ($global_perms_array as $perms_id => $perms_name) {
        $gperm = $gpermHandler->create();
        $gperm->setVar("gperm_groupid", XOOPS_GROUP_ADMIN);
        $gperm->setVar("gperm_name", "myalbum_global");
        $gperm->setVar("gperm_modid", $modid);
        $gperm->setVar("gperm_itemid", $perms_id );
        $gpermHandler->insert($gperm) ;
        unset($gperm);
    }
}

');

function xoops_module_install_myalbum(XoopsModule $xoopsModule)
{
    include_once dirname(dirname(dirname(__DIR__))) . '/mainfile.php';

    xoops_loadLanguage('admin', $xoopsModule->getVar('dirname'));
    xoops_loadLanguage('modinfo', $xoopsModule->getVar('dirname'));

    $moduleDirName = $xoopsModule->getVar('dirname');
    include_once $GLOBALS['xoops']->path('modules/' . $moduleDirName . '/include/config.php');

    foreach (array_keys($uploadFolders) as $i) {
        MyalbumUtilities::createFolder($uploadFolders[$i]);
    }

    $file = _ALMB_ROOT_PATH . '/assets/images/blank.png';
    foreach (array_keys($copyFiles) as $i) {
        $dest = $copyFiles[$i] . '/blank.png';
        MyalbumUtilities::copyFile($file, $dest);
    }

    return true;

}
