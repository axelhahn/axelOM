<?php
/*
 *
 *                        __    __    _______  _______ 
 * .---.-..--.--..-----.|  |  |__|  |       ||   |   |
 * |  _  ||_   _||  -__||  |   __   |   -   ||       |
 * |___._||__.__||_____||__|  |__|  |_______||__|_|__|
 *                       \\\_____ axels OBJECT MANAGER
 * 
 * APP HOME :: Show objects of an app
 * included in home.php
 * 
 */

if(!$acl->canView($sTabApp)){
    include('error403.php');
    return false;
}

$sContextbar='';

$TITLE='<strong>'.icon::get($appmeta->getAppicon()) . $appmeta->getAppname().'</strong>' ;
$BANNER=$appmeta->getApphint();

$s=''
    . $renderAdminLTE->addRow(
        $renderAdminLTE->addCol(
            $renderAdminLTE->getInfobox([
                'type' => '',
                'shadow' => '',
                'icon' => icon::getclass('objects'),
                'iconbg' => '',
                'text' => '{{home.objecttypes}}',
                'number' => (count($appmeta->getObjects()??[]))
                    /*
                    . ($acl->isAppAdmin()
                        ? '<span style="float: right">'
                            .$renderAdminLTE->getButton([
                            'type' => 'success',
                            'text' => icon::get('new').'{{new}} :: TODO',
                            'onclick' => '',
                            ])
                        .'</span>'
                        : ''
                    )
                    */
                    ,
                // 'progressvalue' => 70,
                // 'progresstext' => '70% Increase in 30 Days',
                ])
            , 2
        )
    )
    .'<br>'
;

// $s='TODO: show <strong>app specific</strong> dashboard for app ['.$appmeta->getAppname() .'] ...<br>'
//     .'<br>Appdir: '.$appmeta->getAppdir()
//     .'<br>Configfile: '.$appmeta->getConfigfile()
//     // .'<pre>Config:<br>'.htmlentities( file_get_contents($appmeta->getConfigfile()), 1).'</pre>'
//     // .'<pre>Database settings: '.print_r($appmeta->getDbsettings(), 1).'</pre>'
//     .'<hr>'

include('inc_set_db.php'); // set $oDB
require_once('inc_functions.php');

$sBoxes='';
foreach($appmeta->getObjects() as $sObj=>$aObjData){

    $o=initClass( $oDB, $sObj );
    if(is_object($o)){
        $iCount=$o->count();
        $sItems=$renderAdminLTE->getBadge([
            'type'=>$iCount ? 'success' : 'secondary',
            'title'=>'{{home.count}}: '.$iCount,
            'text'=>' '.$iCount.' ',
        ]);;
    } else {
        $sItems=$renderAdminLTE->getBadge([
            'type'=>'secondary',
            'title'=>'NA',
            'text'=>'NA',
        ]);;
    }
    // $sBoxes.='<strong>'.$sObj.'</strong> - '.$iCount.'<br><pre>'.print_r($aObjData, 1).'</pre>';
    $sBoxes.=$renderAdminLTE->addCol(
            $renderAdminLTE->getCard([
                // 'type' => 'gray',
                'title' => '<a href="?app='.$appmeta->getId().'&page=object&object='.$sObj.'"><i class="'.$aObjData['icon'].'"></i> ' . $aObjData['label'].'</a>' ,
                'text' => ''
                    . ($appmeta->getObjectHint($sObj) ? $appmeta->getObjectHint($sObj).'<br>' : '')
                    . '{{home.items}}: '.$sItems,
                // 'variant' => '',
                'class' => 'height10em'
            ]),
            3
        );
}
$s.=$renderAdminLTE->addRow($sBoxes);

$sContextbar = ''
    .$renderAdminLTE->getCallout([
        'type' => '',
        'title' => icon::get('objects').' {{home.objecttypes}}',
        'text' => '<h3>' . (count($appmeta->getObjects()) ?? 0) . '</h3>'
            . icon::get('database').' <strong>'.preg_replace('/:.*/', '', $appmeta->getDbsettings()['dsn'] ?? '').'</strong><br>'
            . ($acl->isAppAdmin($sTabApp) 
                ? str_replace(';', '<br>', preg_replace('/^.*:/', '', $appmeta->getDbsettings()['dsn'] ?? ''))
                : ''
            )
        ,
    ])
    .$renderAdminLTE->getCallout([
    'type' => '',
    'title' => icon::get('more').'{{more}}',
    'text' => ''
        .($acl->canEdit($sTabApp) 
            ? $renderAdminLTE->getButton([
                'type' => '',
                'text' => icon::get('file').'{{files}}',
                'onclick' => 'location.href=\'?app='.$appmeta->getId().'&page=object&object=pdo_db_attachments\';',
            ]).'<br><br>' 
            : ''
        )
        .($acl->isAppAdmin($sTabApp) 
            ? $renderAdminLTE->getButton([
                'type' => '',
                'text' => icon::get('tools').'{{tools}}',
                'onclick' => 'location.href=\'?app='.$appmeta->getId().'&page=tools\';',
            ]).'<br>'
            .$renderAdminLTE->getButton([
                'type' => '',
                'text' => icon::get('config').'{{config}}',
                'onclick' => 'location.href=\'?app='.$appmeta->getId().'&page=config\';',
            ]).'<br>'
            : ''
        )
        ,
]);

$BODY=$renderAdminLTE->addRow(
    $renderAdminLTE->addCol(
        $s, 
        ($sContextbar ? 10 : 12)
        )
    . ($sContextbar 
        ? $renderAdminLTE->addCol($sContextbar,2)
        : ''
    )
);
