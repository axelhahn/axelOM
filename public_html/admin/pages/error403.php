<?php
/*
 *
 *                        __    __    _______  _______ 
 * .---.-..--.--..-----.|  |  |__|  |       ||   |   |
 * |  _  ||_   _||  -__||  |   __   |   -   ||       |
 * |___._||__.__||_____||__|  |__|  |_______||__|_|__|
 *                       \\\_____ axels OBJECT MANAGER
 * 
 * 403 page
 * 
 */

$TITLE='{{403.title}}';
$BODY=''
    . '<i class="'.icon::getclass('403').'" style="font-size: 800%;"></i><br><br>'
    . $renderAdminLTE->getAlert(array (
            'type' => 'danger',
            'title' => '{{403.subtitle}}',
            'dismissible' => 0,
            'text' => '{{403.message}}',
        )
    )
    .'<br><br>'
    . $renderAdminLTE->getButton([
        'type' => 'secondary',
        'text' => icon::get('back') . '{{back}}',
        'onclick' => 'history.back();',
        ])
    ;
