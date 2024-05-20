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
    '{{DIR_ADMINLTE}}' => $sAppReldir.'/vendor/admin-lte/3.2.0',
    '{{DIR_ADMINLTEPLUGINS}}' => $sAppReldir.'/vendor/admin-lte-plugins',
    '{{PAGE_SKIN}}'    =>'',
    '{{PAGE_TITLE}}'   =>'',
    '{{PAGE_LAYOUT}}'  =>'layout-navbar-fixed layout-fixed sidebar-mini',
    // '{{NAVI_TOP}}'     =>'<nav class="main-header navbar navbar-expand navbar-white navbar-light"><ul class="navbar-nav" id="instances"></ul></nav>',
    '{{BRAND}}'        =>'<a href="?" class="brand-link bg-navy">
                            <i class="fa-solid fa-cubes"></i> 
                            <span class="brand-text">'.APP_NAME.'</span>
                            <span class="brand-text font-weight-light">v'.APP_VERSION.'</span>
                            </a>',
    '{{NAVI_LEFT}}'    =>'
                            <br>
                            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                                <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="far fa-pack"></i>
                                        <p>Server</p>
                                    </a>
                                </li>
                            </ul>
                            <!--                            
                            -->
                            <div class="form-inline">
                                <div class="input-group">
                                    <input id="serverfiltertext" class="form-control form-control-sidebar" type="search" placeholder="Search"

                                        onchange="filterServers();"
                                        onkeypress="filterServers();"
                                        onkeyup="filterServers();"
                
                                        >
                                    <div class="input-group-append">
                                        <button class="btn btn-sidebar"
                                        onclick="$(\'#serverfiltertext\').val(\'\'); filterServers();"                                        >
                                        ×
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <br>

                            <div class="form-inline">
                                <div id="selectserver" class="form-control-sidebar"></div>
                            </div>
                            <br>

                            <div class="sidebar-infobox bottom">
                                <i class="fas fa-info-circle"></i> PDO Class Admin v'.APP_VERSION.'<br>
                                <i class="fab fa-php"></i> PHP '.PHP_VERSION.'<br>
                            </div>
                            ',

    '{{PAGE_HEADER_LEFT}}'=>'{{PAGE_HEADER_LEFT}}',
    '{{PAGE_HEADER_RIGHT}}'=>'{{PAGE_HEADER_RIGHT}}',
    '{{PAGE_BODY}}'=>'{{PAGE_BODY}}',
    // '{{INJECT_JS}}' => $cr->renderJSLang(),
    '{{INJECT_JS}}' => '',
    '{{PAGE_FOOTER_LEFT}}'=>'&copy; 2023-'.date('Y').' - Axel Hahn',
    '{{PAGE_FOOTER_RIGHT}}'=>'Source: <a href="#" target="_blank">TODO</a>',
];