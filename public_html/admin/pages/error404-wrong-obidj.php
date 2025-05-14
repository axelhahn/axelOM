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

$TITLE='{{404-obj-id.title}}';
$BANNER='{{404-obj-id.banner}}';

$BODY=''
    . '<i class="'.icon::getclass('404').'" style="font-size: 800%;"></i><br><br>'
    . $renderAdminLTE->getAlert([
            'type' => 'danger',
            'title' => '{{404-obj-id.subtitle}}',
            'dismissible' => 0,
            'text' => '{{404-obj-id.message}}',
        ]
    )
    .'<br><br>'
    . $renderAdminLTE->getButton([
        'type' => 'secondary',
        'text' => icon::get('back') . '{{back}}',
        'onclick' => 'history.back();',
        ])
    ;
