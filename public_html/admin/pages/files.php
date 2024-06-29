<?php
/*
 *
 *                        __    __    _______  _______ 
 * .---.-..--.--..-----.|  |  |__|  |       ||   |   |
 * |  _  ||_   _||  -__||  |   __   |   -   ||       |
 * |___._||__.__||_____||__|  |__|  |_______||__|_|__|
 *                       \\\_____ axels OBJECT MANAGER
 * 
 * UNUSED
 * 
 */

$sBaseUrl = '?page=files&app='.$sTabApp;

$TITLE='File upload';

$sMainContent = '';


// ---------- let's init

require_once('object__functions.php');
include('inc_set_db.php'); // set $oDB

$o=initClass( $oDB, 'pdo_db_attachments' );
if(is_string($o)) {
    // strng = error occured
    $BODY = $o;
    return true;
}

$sMainContent='Hello';
/*
$sForm='
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="file" />
    </form>
';
$sConent=$renderAdminLTE->getCard(array(
    'type' => 'default',
    'variant' => 'outline',
    // 'tb-remove' => 1,
    // 'tb-collapse' => 1,
    'title' => 'Coming soon: Files and upload',
    // 'tools' => '123',
    'text' => '...',
    'footer' => '',
));
*/

$BODY=$renderAdminLTE->addRow(
    $renderAdminLTE->addCol(
        $sMainContent,
        10
        )
);
