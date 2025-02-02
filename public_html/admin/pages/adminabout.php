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
$BANNER='{{home.banner}}';

$sPerms='';
$s='';


$s.=$renderAdminLTE->getCard([
    'type' => '',
    'title' => '',
    'text' => '{{about.text}}'
    ,
    // 'variant' => '',
]);


$BODY=$renderAdminLTE->addRow(
    $renderAdminLTE->addCol(
        $s, 
        12
        )
);
