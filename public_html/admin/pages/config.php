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
$sHomeAppUrl = '?app='.$sTabApp.'&page=home';

$TITLE='<strong>'
    .'<a href="'.$sHomeAppUrl.'">'
    .icon::get($appmeta->getAppicon()) . $appmeta->getAppname()
    .'</a></strong> ' .DELIM_TITLE
    . icon::get('config') . '{{config.title}}';

$BANNER=$appmeta->getApphint() . ' -> <strong>{{config.banner}}</strong>';

$sFile=__DIR__.'/../../apps/'.$sTabApp.'/config/objects.php';

// ---------- handle POST

if (isset($_POST['action'])) {

    switch ($_POST['action']) {
        case 'save':
            $sFile = $_POST['file'];
            $sContent = $_POST['content'];
            if ($sFile) {
                saveFile($sFile, $sContent);
            } else {
                addMsg('error', '{{msgerr.missing_filename}}');
            }
            break;;

        default:
            http_response_code(400);
            addMsg('error', 'OOPS: POST action [' . htmlentities($_POST['action']) . '] is not handled (yet). :-/');
    }
}


$s=''
    . editorInCard([
        'url' => "?app=$sTabApp&page=config",
        'title' => icon::get('config') . "./apps/$sTabApp/config/objects.php",
        'file' => $sFile,
        'readonly' => $aSettings['editor']['readonly']['appsettings'] ?? false
    ])

    /*
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
    */
;




$BODY=$renderAdminLTE->addRow(
    $renderAdminLTE->addCol(
        renderMsg() . $s, 
        12
        )
);
