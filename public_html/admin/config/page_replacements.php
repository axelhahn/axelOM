<?php
/*
 
                             __    __    _______  _______ 
       .---.-..--.--..-----.|  |  |__|  |       ||   |   |
       |  _  ||_   _||  -__||  |   __   |   -   ||       |
       |___._||__.__||_____||__|  |__|  |_______||__|_|__|
                             \\\_____ axels OBJECT MANAGER
 

       this file is included by ../index.php

*/
$sAppReldir=str_replace('/admin/index.php', '', $_SERVER['SCRIPT_NAME']);
return [
    '{{DIR_ADMINLTE}}' => $sAppReldir.'/vendor/admin-lte/4.0.0-beta1',
    '{{DIR_ADMINLTEPLUGINS}}' => $sAppReldir.'/vendor/admin-lte-plugins',
    '{{HTML_HEAD}}'    =>'<!-- MORE HTML_HEAD DATA -->',
    '{{PAGE_SKIN}}'    =>'',
    '{{PAGE_TITLE}}'   =>'',
    '{{PAGE_LAYOUT}}'  =>'layout-navbar-fixed layout-fixed sidebar-mini',
    // '{{NAVI_TOP}}'     =>'<nav class="main-header navbar navbar-expand navbar-white navbar-light"><ul class="navbar-nav" id="instances"></ul></nav>',
    '{{BRAND}}'        =>'<div class="sidebar-brand bg-navy">
                            <a href="?" class="brand-link bg-navy">
                            <i class="fa-solid fa-cubes"></i> 
                            <span class="brand-text">'.APP_NAME.'</span>
                            <span class="brand-text font-weight-light">v'.APP_VERSION.'</span>
                            </a></div>',
    '{{NAVI_LEFT}}'    =>'',

    '{{PAGE_HEADER_LEFT}}'=>'{{PAGE_HEADER_LEFT}}',
    '{{PAGE_HEADER_RIGHT}}'=>'{{PAGE_HEADER_RIGHT}}',
    '{{PAGE_BODY}}'=>'{{PAGE_BODY}}',
    // '{{INJECT_JS}}' => $cr->renderJSLang(),
    '{{INJECT_JS}}' => '',
    '{{PAGE_FOOTER_LEFT}}'=>'&copy; 2023-'.date('Y').' - Axel Hahn',
    '{{PAGE_FOOTER_RIGHT}}'=>'Source: <a href="https://github.com/axelhahn/axelOM/" target="_blank">Github: axelhahn/axelOM</a>',
];