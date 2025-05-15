<?php
/*
 *
 *                        __    __    _______  _______ 
 * .---.-..--.--..-----.|  |  |__|  |       ||   |   |
 * |  _  ||_   _||  -__||  |   __   |   -   ||       |
 * |___._||__.__||_____||__|  |__|  |_______||__|_|__|
 *                       \\\_____ axels OBJECT MANAGER
 * 
 * TOOLS :: Backup + Resore
 * 
 */

if(!$acl->isAppAdmin($sTabApp)){
    include('error403.php');
    return false;
}

require_once('inc_functions.php');

$sContextbar='';
$sBackuppath=dirname(dirname(__DIR__)).'/apps/'.$sTabApp.'/data/';

// $sBaseAppUrl = '?page=tools&app='.$sTabApp;
// $sBaseUrl    = '?page=tools&app='.$sTabApp;
$sHomeAppUrl = '?app='.$sTabApp.'&page=home';

$TITLE='<strong>'
    .'<a href="'.$sHomeAppUrl.'">'
    .icon::get($appmeta->getAppicon()) . $appmeta->getAppname()
    .'</a></strong> ' .DELIM_TITLE
    . icon::get('tools').'{{tools}}' ;

$BANNER=$appmeta->getApphint().' -> <strong>{{tools}}</strong>';

if($sTabApp){

    if(!$acl->isAppAdmin($sTabApp)){
        $BODY='';
        return false;
    }
    
    // ----------------------------------------------------------------------
    // Actions
    // ----------------------------------------------------------------------
    $sAction=queryparam::post('action', '/^[a-z]*$/');

    $sOutAction='';

    include('inc_set_db.php'); // set $oDB

    if($sAction){
        // $sOutAction.='ACTION = '.$sAction.'<br>';
        $sFile=queryparam::post('file', '/^[a-z\.0-9\-]*$/');
        $sBackupfile=$sBackuppath.'/'.$sFile;

        switch ($sAction) {
            case 'backup':
                $TITLE.=DELIM_TITLE . ' {{backup}}';
                $sBackupfile=$sBackuppath.'/'.$sTabApp.'-'.$oDB->driver().'-'.date('U').'.json';
                $aDumpdata=$oDB->dump();
                if(isset($aDumpdata)){

                    if (file_put_contents($sBackupfile, json_encode($aDumpdata, JSON_PRETTY_PRINT))){
                        addMsg('ok', '{{msgok.tools_backup_created}}');
                        $sOutAction.='{{msgok.tools_backup_created}}<br>';
                    } else {
                        addMsg('error', '{{msgerr.tools_backup_file_not_written}}');
                    }
                } else {
                    addMsg('error', '{{msgerr.tools_backup_no_data}}');
                }
                break;
            case 'restore':
                    $sBackupfile=$sBackuppath.'/'.$sFile;
                    $oDB->setDebug(1); 
                    
                    if ($oDB->import($sBackupfile)){
                        addMsg('ok', '{{msgok.tools_backup_restored}}');
                    } else {
                        addMsg('error', '{{msgerr.tools_restore_failed}}');
                    }
                    $oDB->setDebug(0);
                    break;
            case 'delete':
                $sBackupfile=$sBackuppath.'/'.$sFile;
                if (file_exists($sBackupfile)){
                    if (unlink($sBackupfile)){
                        addMsg('ok', '{{msgok.tools_file_deleted}}');
                    } else {
                        addMsg('error', '{{msgerr.tools_file_not_deleted}}');
                    }
                } else {
                    addMsg('error', '{{msgerr.tools_file_not_deleted_not_found}}');
                }
                break;
            case 'view':


                $sBackupfile=$sBackuppath.'/'.$sFile;
                /*

                // does not work:
                header("Content-Type: text/json");
                header('Content-Disposition: attachment; filename="'.$sFile.'"');
                readfile($sBackupfile);
                die();
                */

                $sOutAction.= htmlentities(file_get_contents($sBackupfile));
                break;
            case 'optimize':
                $TITLE.=DELIM_TITLE . ' {{optimize}}';
                // $oDB->setDebug(1);                
                $sOutAction.=print_r($oDB->optimize(), 1);
                // $oDB->setDebug(0);
                break;
            default:
                addMsg('error', '{{msgerr.action_not_implemented}}: ['.$sAction.']');
        }
    }

    if($sOutAction){
        $BODY=$renderAdminLTE->getCard([
            'type' => 'info',
            'variant' => 'outline',
            // 'tb-remove' => 1,
            // 'tb-collapse' => 1,
            'title' => '{{tools.output}}',
            'tools' => '',
            'text' => ''
                .$renderAdminLTE->getButton([
                    'type' => 'dark',
                    'text' => icon::get('back') . '{{back}}',
                    'onclick' => 'location.reload();',
                ]).'<br><hr>'                
                ."<pre>$sOutAction</pre>"
        ]);
        return true;
    }

    // ----------------------------------------------------------------------
    // APP HOME :: Show backups of an app
    // ----------------------------------------------------------------------
    
    $s='';

    $sTable = '';
    foreach (glob($sBackuppath."/*json") as $file) {
        $sTable .= '<tr>'
        . '<td>' . basename($file) . '</td>'
        . '<td>' . date('Y-m-d H:i:s', filemtime($file)) . '</td>'
        . '<td align="right">' . number_format( filesize($file), false, false,"'") . '</td>'
        . '<td align="right"><nobr>' 
                . $renderAdminLTE->getButton([
                    'class' => 'btn-outline-dark',
                    'text' => icon::get('view') . '{{view}}',
                    'title' => '{{view}}: '.$file,
                    'onclick' => 'httprequest(\'POST\', location.href , {\'action\': \'view\', \'file\': \''.basename($file).'\'});',
                ])
                .' '
                . $renderAdminLTE->getButton([
                    'class' => 'btn-outline-dark',
                    'text' => icon::get('restore') . '{{restore}}',
                    'title' => '{{restore}}: '.$file,
                    'onclick' => 'if(confirm(\'{{confirm_restore}}\n\n'.$file.'\n\n?\')) httprequest(\'POST\', location.href , {\'action\': \'restore\', \'file\': \''.basename($file).'\'});',
                ])
                .' '
                .$renderAdminLTE->getButton([
                    'class' => 'btn-outline-danger',
                    'text' => icon::get('delete') . '{{delete}}',
                    'title' => '{{delete}}: '.basename($file),
                    'onclick' => 'if(confirm(\'{{confirm_delete}}\n\n'.$file.'\n\n?\')) httprequest(\'POST\', location.href , {\'action\': \'delete\', \'file\': \''.basename($file).'\'});',
                ])

            . '</nobr></td>' . "\n";

        $sTable .= '</tr>' . "\n";
    }

    $sTable = $sTable 
        ? '<table class="table table-bordered table-striped dataTable dtr-inline">
        <thead>
            <tr>
                <th>{{backup.file}}</th>
                <th>{{backup.timestamp}}</th>
                <th>{{backup.size}}</th>
                <th class="actions">{{td-actions}}</th>
            </tr>
        </thead>
        <tbody>' . "\n" 
        . $sTable 
        . '</tbody></table>' . "\n\n" 
        : '';

    $s .= ''
        .$renderAdminLTE->getCard([
            'type' => '',
            'variant' => 'outline',
            // 'tb-remove' => 1,
            // 'tb-collapse' => 1,
            // 'title' => '{{tools.manage_backups}}',
            'tools' => '',
            'text' => ''
                // backup button:
                . $renderAdminLTE->getButton([
                    'text' => icon::get('backup').'<br>{{backup}}',
                    'onclick' => 'httprequest(\'POST\', location.href , {\'action\': \'backup\'})',
                ])
                . ' '
                . $renderAdminLTE->getButton([
                    'text' => icon::get('optimize').'<br>{{optimize}}',
                    'onclick' => 'httprequest(\'POST\', location.href , {\'action\': \'optimize\'})',
                ])                    
                . ' '
                . '<br>'
                    
            // 'footer' => '&copy; Axel',
        ])
        .$renderAdminLTE->getCard([
        'type' => 'info',
        'variant' => 'outline',
        // 'tb-remove' => 1,
        // 'tb-collapse' => 1,
        'title' => '{{tools.manage_backups}}',
        'tools' => '',
        'text' => ''
            // backup button:
            /*
            . $renderAdminLTE->getButton([
                    'type' => 'success',
                    'text' => icon::get('backup').'{{backup}}',
                    'onclick' => 'httprequest(\'POST\', location.href , {\'action\': \'backup\'})',
                ]) . '<br><br>'
            */  
            . $sTable
        // 'footer' => '&copy; Axel',
    ]);


    $sContextbar = '';
    /*
    $sContextbar = $renderAdminLTE->getCallout([
        'type' => '',
        'title' => icon::get('more').'{{more}}',
        'text' => ''
            . $renderAdminLTE->getButton([
                'class' => 'btn',
                'text' => icon::get('optimize').'{{optimize}}',
                'onclick' => 'httprequest(\'POST\', location.href , {\'action\': \'optimize\'})',
            ]) . '<br><br>'
    ]);
    */
}


$BODY=$renderAdminLTE->addRow(
    $renderAdminLTE->addCol(
        renderMsg().$s, 
        ($sContextbar ? 10 : 12)
        )
    . ($sContextbar 
        ? $renderAdminLTE->addCol($sContextbar,2)
        : ''
    )
);
