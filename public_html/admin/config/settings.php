<?php
/*
 
                             __    __    _______  _______ 
       .---.-..--.--..-----.|  |  |__|  |       ||   |   |
       |  _  ||_   _||  -__||  |   __   |   -   ||       |
       |___._||__.__||_____||__|  |__|  |_______||__|_|__|
                             \\\_____ axels OBJECT MANAGER
 

       this file is included by ../index.php

*/
return [

    // show debug window?
    'debug' => true,

    // language of backnd ui
    // 'lang' => 'en-en',
    'lang' => 'de-de',

    // just an idea...
    'acl'=>[
        'userfield'=>'REMOTE_USER',
        'groups'=>[
            'admin'=>[],
            'manager'=>[],
            'guest'=>[],
        ],
        'rules'=> [
            'pages'=>[
                'home'=> ['.*'],
                'object' => ['@admin', '@manager'],
                'tools'=>['@admin'],
            ],
            'objects'=>[],
        ],
    ],
];