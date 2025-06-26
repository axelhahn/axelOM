<?php
/*
 *
 *                        __    __    _______  _______ 
 * .---.-..--.--..-----.|  |  |__|  |       ||   |   |
 * |  _  ||_   _||  -__||  |   __   |   -   ||       |
 * |___._||__.__||_____||__|  |__|  |_______||__|_|__|
 *                       \\\_____ axels OBJECT MANAGER
 * 
 * OBJECT EDITOR :: 
 * - see config
 * - repair
 *  
 */

 if(!$acl->canEdit($sTabApp)){
    include('error403.php');
    return false;
}

require_once('inc_functions.php');

$sBaseUrl    = '?app='.$sTabApp.'&page='.$sPage.'&object=' . $sObject;
$sObjectUrl  = '?app='.$sTabApp.'&page=object&object=' . $sObject;

$aObjdata=$appmeta->getObject($sObject);
$sObjLabel= ''
    . (isset($aObjdata['icon']) ? icon::get($aObjdata['icon']) : '')
    . ($aObjdata['label'] ?? '');

$TITLE=''.icon::get($appmeta->getAppicon()) . $appmeta->getAppname().' ' 
    . '<strong>'.($sObjLabel ? '  ' . $sObjLabel : '') .'</strong> ';
$BANNER='{{home.banner}}';


// addMsg('ok', 'OK: I am a test');

$sMainContent = '';
$sContextbar='';


include('inc_set_db.php'); // set $oDB
$o=initClass( $oDB, $sObject );
if(is_string($o)) {
    // string = error occured
    $BODY = $o;
    return true;
}

// ---------- handle POST
$sPostOut='';
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'repair':

            $sDumpfile=sys_get_temp_dir() . '/'. $sObject . '_'.microtime(true).'.jsonlines';
            $sPostOut.="Dumping to $sDumpfile... <br>";
            $oDB->dump($sDumpfile, [$sObject]);

            $sPostOut.="Dropping table ...<br>";
            if(!is_array($oDB->makeQuery('DROP table ' . $sObject))){
                addMsg('error', 'Repair: DROP table failed!');
            }

            $sPostOut.='Initializing object "'.$sObject.'"...<br>';
            if(!$o=initClass( $oDB, $sObject )){
                addMsg('error', 'Repair: Unable to initialize object "'.$sObject.'"!');
            }

            $sPostOut.="Importing data from $sDumpfile...<br>";
            if(!$oDB->import($sDumpfile)){
                addMsg('error', 'Repair: IMPORT of dump file failed!');
            }

            $sPostOut.="Removing $sDumpfile...<br>";
            unlink($sDumpfile);
            $sPostOut.="Done!<br>";

            addMsg('ok', $sPostOut);
            break;;

        default:
            header('http/1.0 400 Bad Request');
            addMsg('error', 'OOPS: POST action [' . htmlentities($_POST['action']) . '] is not handled (yet). :-/');
    }
}

// ---------- MAIN

$aCheck=$o->verifyColumns();
$bDbTableOk=isset($aCheck['_result']['errors']) && $aCheck['_result']['errors']==0;
if(!$bDbTableOk){
    addMsg(
        'error', 
        '{{object.tablecheck_update_required}}<ul><li>'.implode('</li><li>', $aCheck['_result']['messages']).'</li></ul>'
        .$renderAdminLTE->getButton([
            'type' => 'dark',
            'text' => icon::get('repair').'{{repair}}',
            'onclick' => 'httprequest(\'POST\', location.href , {\'action\': \'repair\'});',

        ]) . ' '
        ,
    );
}


$sMainContent .= ''
. ($appmeta->getApphint() && $appmeta->getApphint() 
    ? $renderAdminLTE->getCallout(array (
        'type' => 'gray',
        'text' => $appmeta->getApphint() . ' -> <strong>' . $appmeta->getObjectHint($sObject).'</strong>',
    )).'<br>'
    : '');


$aAttributes = $o->getAttributes(true);

$sProperties='';
foreach($aAttributes as $sKey=>$aProperty){
    $sProperties.='<strong>'.$sKey.'</Strong>: <em>'.$aProperty['create'].'</em><br>';
}

$sCfgdata='<pre class="config hidden" id="objattributes">{{properties}}:<br><br>'.print_r($o->getAttributes(true), 1).'</pre>';

$sMainContent.= $renderAdminLTE->addRow(
    
    $renderAdminLTE->addCol(
        $renderAdminLTE->getCard([
            'type' => 'info',
            'variant' => 'outline',
            'title' => icon::get('properties').'{{properties}}',
            'text' => $sProperties .'<hr>'
                .$renderAdminLTE->getButton([
                    'class' => 'btn-outline-dark',
                    'text' => icon::get('code').'{{raw}}',
                    'onclick' => '$(\'#objattributes\').toggleClass(\'hidden\');',
                ]). '<br><br>'.$sCfgdata,
            // 'footer' => $sFooterRelations,
        ]),
        12
    )
);

$sContextbar .= $renderAdminLTE->getCallout([
        'type' => '',
        'title' => icon::get('items').'{{items}}',
        'text' => '<h3>' . $o->count() . '</h3><hr>'
            .$renderAdminLTE->getButton([
                'class' => 'btn-outline-dark',
                'text' => icon::get('edit').' {{edit}}',
                'onclick' => 'document.location.href=\''.$sObjectUrl.'\';',
            ]),

        $sObjectUrl
        ,
    ])
    .($bDbTableOk
        ? $renderAdminLTE->getCallout([
            'type' => '',
            'title' => icon::get('database').'{{object.tablecheck}}',
            'text' => '<h3>{{object.tablecheck_ok}}</h3>'
        
        ])
        : $renderAdminLTE->getAlert([
            'type' => 'danger',
            'title' => '{{error}}',
            'text' => icon::get('database').'{{object.tablecheck}}<br>'
                .'{{object.tablecheck_update_required}}'
        
        ])
    )
    ;


$BODY = $renderAdminLTE->addRow(
    $renderAdminLTE->addCol(
        renderMsg().$sMainContent,
        10
    )
    . $renderAdminLTE->addCol(
        $sContextbar,
        2
    )
);
