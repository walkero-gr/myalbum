<?php

// defined('XOOPS_ROOT_PATH') || exit('XOOPS root path not defined');

$moduleDirName = basename(dirname(__DIR__));
if (!preg_match('/^(\D+)(\d*)$/', $moduleDirName, $regs)) {
    echo('invalid dirname: ' . htmlspecialchars($moduleDirName));
}
$mydirnumber = $regs[2] === '' ? '' : (int)$regs[2];

// referer check
$ref = xoops_getenv('HTTP_REFERER');
if ($ref == '' || strpos($ref, XOOPS_URL . '/modules/system/admin.php') == 0) {
    /* module specific part */
    $db = XoopsDatabaseFactory::getDatabaseConnection();

    // 2.8 -> 2.9
    $check_result = $db->query('SELECT weight FROM ' . $db->prefix("myalbum{$mydirnumber}_cat"));
    if (!$check_result) {
        $db->query('ALTER TABLE ' . $db->prefix("myalbum{$mydirnumber}_cat")
                   . " ADD weight int(5) unsigned NOT NULL default 0, ADD depth int(5) unsigned NOT NULL default 0, ADD description text, ADD allowed_ext varchar(255) NOT NULL default 'jpg|jpeg|gif|png', ADD KEY (weight), ADD KEY (depth)");
        $db->query('ALTER TABLE ' . $db->prefix("myalbum{$mydirnumber}_photos") . ' ADD KEY (`date`)');
        $db->query('ALTER TABLE ' . $db->prefix("myalbum{$mydirnumber}_text") . ' DROP KEY lid');
        $db->query('ALTER TABLE ' . $db->prefix("myalbum{$mydirnumber}_text") . ' ADD PRIMARY KEY (lid)');
        $db->query('ALTER TABLE ' . $db->prefix("myalbum{$mydirnumber}_votedata") . ' ADD KEY (lid)');
    }

    /* General part */
    // Version 3.01
    $db->query('ALTER TABLE ' . $db->prefix("myalbum{$mydirnumber}_photos") . " ADD COLUMN tags varchar(255) NOT NULL default ''");
    // Keep the values of block's options when module is updated (by nobunobu)
    include __DIR__ . '/updateblock.inc.php';
}

function xoops_module_update_myalbum(XoopsModule $module, $oldversion = null)
{
    //create upload directories, if needed
    $moduleDirName = $module->getVar('dirname');
    include $GLOBALS['xoops']->path('modules/' . $moduleDirName . '/include/config.php');

    foreach (array_keys($uploadFolders) as $i) {
        MyalbumUtilities::createFolder($uploadFolders[$i]);
    }
    //copy blank.png files, if needed
    $file = _ALBM_ROOT_PATH . '/assets/images/blank.png';
    foreach (array_keys($copyFiles) as $i) {
        $dest = $copyFiles[$i] . '/blank.png';
        MyalbumUtilities::copyFile($file, $dest);
    }

    $gpermHandler = xoops_getHandler('groupperm');

    return $gpermHandler->deleteByModule($module->getVar('mid'), 'item_read');
}
