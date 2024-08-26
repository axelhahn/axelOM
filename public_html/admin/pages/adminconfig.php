<?php
/*
 *
 *                        __    __    _______  _______ 
 * .---.-..--.--..-----.|  |  |__|  |       ||   |   |
 * |  _  ||_   _||  -__||  |   __   |   -   ||       |
 * |___._||__.__||_____||__|  |__|  |_______||__|_|__|
 *                       \\\_____ axels OBJECT MANAGER
 * 
 * ADMIN GLOBAL CONFIG :: Edit global config
 * 
 */
include "inc_functions.php";

if (!$acl->isAdmin()) {
    include('error403.php');
    return false;
}

$sContextbar = '';
$TITLE = icon::get('config') . '{{globalconfig.title}}';
$BANNER='{{globalconfig.banner}}';

$sFile = __DIR__ . '/../config/settings.php';

// ---------- handle POST

if (isset($_POST['action'])) {

    switch ($_POST['action']) {
        case 'save':
            $sFile = $_POST['file'];
            $sContent = $_POST['content'];
            if ($sFile) {
                saveFile($sFile, $sContent);
            } else {
                addMsg('error', '{{msgerr.missing_filename}}');
            }
            break;;

        default:
            header('http/1.0 400 Bad Request');
            addMsg('error', 'OOPS: POST action [' . htmlentities($_POST['action']) . '] is not handled (yet). :-/');
    }
}

// ---------- MAIN

$s = ''
    . editorInCard([
        'url' => '?page=adminconfig',
        'title' => icon::get('config') . "./config/settings.php",
        'file' => $sFile,
        'readonly' => $aSettings['editor']['readonly']['globalsettings'] ?? false
    ])
;

// ---------- output

$BODY = $renderAdminLTE->addRow(
    $renderAdminLTE->addCol(
        renderMsg() . $s,
        12
    )
);
