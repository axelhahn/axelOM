<?php
/*
 *
 *                        __    __    _______  _______ 
 * .---.-..--.--..-----.|  |  |__|  |       ||   |   |
 * |  _  ||_   _||  -__||  |   __   |   -   ||       |
 * |___._||__.__||_____||__|  |__|  |_______||__|_|__|
 *                       \\\_____ axels OBJECT MANAGER
 * 
 * SET $oDB
 * 
 * USAGE:
 * include('inc_set_db.php'); // set $oDB
 * before using objects of class axelhahn\pdo_db
 * 
 */

require_once __DIR__ . '/../../vendor/php-abstract-dbo/src/pdo-db.class.php';
$oDB = new axelhahn\pdo_db([
    'db'=>$appmeta->getDbsettings(),
    // 'showdebug'=>true,
    'showerrors' => true,
]);

if (!$oDB) {
    if($renderAdminLTE){
        $BODY = $renderAdminLTE->getAlert([
            'type' => 'danger',
            'title' => 'Unable to initialize database.',
            'dismissible' => 0,
            'text' => 'ERROR: ' . $DB->lasterror(),
        ]);
    } else {
        // die('ERROR: ' . $DB->lasterror());
    }
    return true;
}
