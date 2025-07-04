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

$sDumpExtension='jsonlines';
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
    $sAction=queryparam::post('action', '/^[a-z_]*$/');

    $sOutAction='';

    include('inc_set_db.php'); // set $oDB

    if($sAction){
        $sFile=queryparam::post('file', '/^[a-z\.0-9\-]*$/');
        $sBackupfile="$sBackuppath/$sFile";

        switch ($sAction) {
            case 'backup':
                $aTables=(array) queryparam::post('tables');

                $TITLE.=DELIM_TITLE . ' {{backup}}';
                $sBackupfile=$sBackuppath.'/'.$sTabApp.'-'.$oDB->driver().'-'.date('U').'.'.$sDumpExtension;
                if($oDB->dump($sBackupfile, $aTables)){
                    addMsg('ok', '{{msgok.tools_backup_created}}');
                    $sOutAction.='{{msgok.tools_backup_created}}<br>';
                } else {
                    addMsg('error', '{{msgerr.tools_backup_file_not_written}}');
                }
                break;

            case 'restore_custom':

                $aOptions=$oDB->dumpAnalyzer($sBackuppath.'/'.$sFile);
                // echo '<pre>'.print_r($aOptions, 1).'</pre>';
                $sFormCustomRestore='<form method="POST" id="backup" action="">'
                    . '<input type="hidden" name="action" value="restore">'
                    . '<input type="hidden" name="file" value="'.$sFile.'">'
                    .icon::get('file')."$sBackuppath/<strong>$sFile</strong><br>"
                    .icon::get('clock').$aOptions['meta']['timestamp'].'<br>'
                    .icon::get('database').$aOptions['meta']['driver']
                    .($oDB->driver()==$aOptions['meta']['driver'] ? '' : ' --> '.$oDB->driver()).'<br>'
                    .($aOptions['completed'] ? icon::get('yes') . '{{tools.dump-completed}}' : icon::get('no') . '{{tools.dump-incomplete}}').'<br>'
                    ."<br>"
                ;
                $iRowsTotal=0;
                foreach($aOptions['rows']??[] as $sTable => $iRows){
                    $iRowsTotal+=$iRows;
                    $sFormCustomRestore.= 
                        "... $sTable (<strong>$iRows</strong>)<br>"
                        // . '<label><input type="checkbox" name="tables['.$sTable.'][drop]" value="true" /> {{tools.restore-global-drop}}<br></label><br>'
                        // . '<label><input type="checkbox" name="tables['.$sTable.'][create-if-not-exist]" value="true" checked /> {{tools.restore-global-create-if-not-exist}}<br></label><br>'
                        // . '<label><input type="checkbox" name="tables['.$sTable.'][import]" value="true" checked /> {{tools.restore-global-import}}<br></label><br>'
                        ;
                }
                $sFormCustomRestore.= "<br>{{tools.dump-total-rows}}: <strong>$iRowsTotal</strong><br>";
                if(!$iRowsTotal){                
                    addMsg('error', "{{tools.dump-total-rows}}: <strong>$iRowsTotal</strong>");
                } else {
                    $sFormCustomRestore.=""
                    ."<br><fieldset>"
                        ."<strong>{{tools.restore-global-options}}</strong>:<br>"
                        . '<label><input type="checkbox" name="global[drop]"   value="true" checked/> {{tools.restore-global-drop}}<br></label><br>'
                        . '<label><input type="checkbox" name="global[create]" value="true" checked /> {{tools.restore-global-create-if-not-exist}}<br></label><br>'
                        . '<label><input type="checkbox" name="global[import]" value="true" checked /> {{tools.restore-global-import}}<br></label><br>'
                    ."</fieldset>"
                    ;
                }

                $sFormCustomRestore.='<br>'.$renderAdminLTE->getButton([
                        'type' => 'secondary',
                        'text' => icon::get('abort') . '{{abort}}',
                        'onclick' => 'location.href=\'\'; return false;',
                    ])
                    .($iRowsTotal
                        ? ' '
                            . $renderAdminLTE->getButton([
                                'type' => 'primary',
                                'text' => icon::get('restore') . '{{restore}}',
                            ])
                        : ''
                    )
                    .'</form>';


                // $BODY='<pre>'.print_r($aOptions, 1).'</pre>';
                $BODY=$renderAdminLTE->getCard([
                    'type' => 'info',
                    'variant' => 'outline',
                    // 'tb-remove' => 1,
                    // 'tb-collapse' => 1,
                    'title' => '{{restore}}',
                    'tools' => '',
                    'text' => $sFormCustomRestore
                ]);
                return true;
                break;

            case 'restore':
                $aOptions=[
                    'global' => (array) queryparam::post('global')??[],
                    'tables' => (array) queryparam::post('tables')??[],
                ];

                // if the chckbox was disabled then no value was sent.
                // --> set it to false
                $aOptions['global']['drop']??=false;
                $aOptions['global']['create']??=false;
                $aOptions['global']['import']??=false;

                $sBackupfile=$sBackuppath.'/'.$sFile;
                // $oDB->setDebug(1);

                // echo "$sBackupfile<br>";
                // print_r($aOptions); die();
                
                if ($oDB->import($sBackupfile, $aOptions)){
                    addMsg('ok', '{{msgok.tools_backup_restored}}');
                } else {
                    addMsg('error', '{{msgerr.tools_restore_failed}}');
                }
                // $oDB->setDebug(0);
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

                $sBackupfile="$sBackuppath/$sFile";

                header("Content-Type: text/json");
                // header('Content-Disposition: attachment; filename="'.$sFile.'"');
                // echo '<link rel="stylesheet" href="/vendor/admin-lte/4.0.0-beta1/css/adminlte.min.css">';
                // echo '<link rel="stylesheet" href="main.css">';
                echo '<div ondblclick="location.reload();">'
                    .'<pre>'
                    .realpath($sBackuppath)."/<strong>".basename($sBackupfile)."</strong><br>"
                    .date("Y-m-d H:i:s", filemtime($sBackupfile))
                    ." :: "
                    .filesize($sBackupfile)." byte<br>"
                    .'</pre>'
                    ."<button onclick='location.reload();' style='padding: 0.6em 1em;'>&laquo; back</button><br>"
                    ."<textarea readonly style='width:100%;height:85%;'>";
                readfile($sBackupfile);
                echo "</textarea></div>";
                die();

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
    foreach (glob($sBackuppath."/*.$sDumpExtension") as $file) {
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
                    // 'onclick' => 'if(confirm(\'{{confirm_restore}}\n\n'.$file.'\n\n?\')) httprequest(\'POST\', location.href , {\'action\': \'restore_custom\', \'file\': \''.basename($file).'\'});',
                    'onclick' => 'httprequest(\'POST\', location.href , {\'action\': \'restore_custom\', \'file\': \''.basename($file).'\'});',
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
        : '{{tools.no_backup_yet}}';

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
                    'class' => 'btn btn-outline-dark',
                    'text' => icon::get('backup').'<br>{{backup}}',
                    'onclick' => 'httprequest(\'POST\', location.href , {\'action\': \'backup\'})',
                ])
                /*
                . ' '
                . $renderAdminLTE->getButton([
                    'class' => 'btn btn-outline-dark',
                    'text' => icon::get('backup').'<br>{{backup_custom}}',
                    'onclick' => 'httprequest(\'POST\', location.href , {\'action\': \'backup_custom\'})',
                ])
                */
                . ' '
                . $renderAdminLTE->getButton([
                    'class' => 'btn btn-outline-dark',
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
