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

if(!$acl->isAdmin()){
    include('error403.php');
    return false;
}

$sContextbar='';
$TITLE=icon::get('Config') . '{{globalconfig.title}}';

$sFile=__DIR__.'/../config/settings.php';

$s=''. $renderAdminLTE->getCallout([
    'type' => 'gray',
    'text' => '{{globalconfig.subtitle}}',
    ]).'<br><br>'

    .$renderAdminLTE->getCard([
        'type' => '',
        'title' => "EDIT $sFile",
        'text' => ''
        
        // init form
        . editorInit(['file'=>$sFile])

        .'<hr>'
        // end form
        
        ,
        'footer' => 'TODO: <button class="btn btn-primary">Save</button>',
        // 'variant' => '',
    ])

;




$BODY=$renderAdminLTE->addRow(
    $renderAdminLTE->addCol(
        $s, 
        12
        )
);
