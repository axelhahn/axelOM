<?php
/*
 *
 *                        __    __    _______  _______ 
 * .---.-..--.--..-----.|  |  |__|  |       ||   |   |
 * |  _  ||_   _||  -__||  |   __   |   -   ||       |
 * |___._||__.__||_____||__|  |__|  |_______||__|_|__|
 *                       \\\_____ axels OBJECT MANAGER
 * 
 * 404 page
 * 
 */

$TITLE='{{404-app.title}}';
$BANNER='{{404-app.banner}}';

$BODY=''
    . '<i class="'.icon::getclass('404').'" style="font-size: 800%;"></i><br><br>'
    . $renderAdminLTE->getAlert([
            'type' => 'danger',
            'title' => '{{404-app.subtitle}}',
            'dismissible' => 0,
            'text' => '{{404-app.message}}',
        ]
    )
    .'<br><br>'
    . $renderAdminLTE->getButton([
        'type' => 'secondary',
        'text' => icon::get('back') . '{{back}}',
        'onclick' => 'history.back();',
        ])
    ;
