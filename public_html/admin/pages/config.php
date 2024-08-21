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

if(!$acl->isAppAdmin()){
    include('error403.php');
    return false;
}

$sContextbar='';
$TITLE=icon::get('Config') . '{{config.title}}';

$sFile=__DIR__.'/../../apps/'.$sTabApp.'/config/objects.php';

$s=''. $renderAdminLTE->getCallout([
    'type' => 'gray',
    'text' => '{{config.subtitle}}',
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
        'footer' => 'TODO <button class="btn btn-primary">Save</button>',
        // 'variant' => '',
    ])

;




$BODY=$renderAdminLTE->addRow(
    $renderAdminLTE->addCol(
        $s, 
        12
        )
);
