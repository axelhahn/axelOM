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

$TITLE='404 :: Page not found';
$BODY=''
    . '<i class="fa-solid fa-bug" style="font-size: 800%;"></i><br><br>'
    . $renderAdminLTE->getAlert(array (
            'type' => 'danger',
            'title' => 'The file was not found.',
            'dismissible' => 0,
            'text' => 'Please check the url or start again from <a href="?">HOME</a>.',
        )
    )
    ;