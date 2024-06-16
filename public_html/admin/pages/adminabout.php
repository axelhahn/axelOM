<?php
/*
 *
 *                        __    __    _______  _______ 
 * .---.-..--.--..-----.|  |  |__|  |       ||   |   |
 * |  _  ||_   _||  -__||  |   __   |   -   ||       |
 * |___._||__.__||_____||__|  |__|  |_______||__|_|__|
 *                       \\\_____ axels OBJECT MANAGER
 * 
 * ADMIN ABOUT
 * 
 */


$sContextbar='';
$TITLE=icon::get('about') . '{{about.title}}';

$sPerms='';
$s='';


$s.=''
. $renderAdminLTE->getCallout([
    'type' => 'info',
    'text' => '{{home.banner}}',
    ]).'<br><br>'
    ;


$s.=$renderAdminLTE->getCard(array (
    'type' => '',
    'title' => '',
    'text' => '{{about.text}}'
    ,
    // 'variant' => '',
));


$BODY=$renderAdminLTE->addRow(
    $renderAdminLTE->addCol(
        $s, 
        12
        )
);
